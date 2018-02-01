<?php
/**
* +---------------------------------------------------------------------------+
* | Copyright (c) 2015, Jort de Vreeze                                        |
* | All rights reserved.                                                      |
* |                                                                           |
* | Redistribution and use in source and binary forms, with or without        |
* | modification, are not permitted.                                          |
* +---------------------------------------------------------------------------+
* | jExp 1.0                                                                  |
* +---------------------------------------------------------------------------+
* | Page.php                                                                  |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * Page controller
 */
class Controller_Page extends Base_Controller
{
    
    /**
     * View all experiments
     * 
     * @return void 
     */
    public function index()
    {
        $records = [];
        
        $experimentModel = new Model_Experiment($this->getConfiguration('model'));
        $sessionModel = new Model_Session($this->getConfiguration('model'));
        
        $experiments = $experimentModel->getAllExperiments();
                
        foreach($experiments as $experiment) {
            $session = $sessionModel->getAllSessionsByExperiment($experiment['identifier']);
            $records[] = $session->num_rows;
        }
        
        $view = $this->getView();
        $view->experiments = $experiments;
        $view->records = $records;
    }
    
    /**
     * Create a new experiment
     * 
     * @return void 
     */
    public function create()
    {
        if (false == $this->isPost()) {
            $view = $this->getView();
        } else {
            $name = filter_input(INPUT_POST, 'name');
            if (null !== $name) {
                $experimentModel = new Model_Experiment($this->getConfiguration('model'));
                $experimentModel->addExperiment($name);
            }
            $this->redirect('index');
        }        
    }  
    
    /**
     * Edit the experiment name
     * 
     * @param  integer $id
     * @return void 
     */
    public function edit($id = null)
    {
        if (false == $this->isPost()) {            
            if (null !== $id) {
                $experimentModel = new Model_Experiment($this->getConfiguration('model'));
                $view = $this->getView();
                $view->experiment = $experimentModel->getExperimentById($id);
            }            
        } else {
            $name = filter_input(INPUT_POST, 'name');
            if (null !== $name && null !== $id) {
                $experimentModel = new Model_Experiment($this->getConfiguration('model'));
                $experimentModel->updateExperimentById($id, $name);
            }
            $this->redirect('index');
        }
    }
    
    /**
     * Delete the experiment 
     * 
     * @param  integer $id
     * @return void 
     */
    public function delete($id = null)
    {
        if (null !== $id) {
            $experimentModel = new Model_Experiment($this->getConfiguration('model'));
            $sessionModel = new Model_Session($this->getConfiguration('model'));
            
            $experiment = $experimentModel->getExperimentById($id);
            
            $sessionModel->deleteSessionsByExperiment($experiment['identifier']);
            $experimentModel->deleteExperimentById($id);          
        }
        $this->redirect('index');
    }
    
    /**
     * Delete all sessions in the experiment 
     * 
     * @param  integer $id
     * @return void 
     */
    public function clear($id = null)
    {
        if (null !== $id) {
            
            $experimentModel = new Model_Experiment($this->getConfiguration('model'));
            $sessionModel = new Model_Session($this->getConfiguration('model'));
            
            $experiment = $experimentModel->getExperimentById($id);
            
            $sessionModel->deleteSessionsByExperiment($experiment['identifier']);         
        }
        $this->redirect('index');
    } 
    
    /**
     * Export the experiment data to a CSV format and put it into a zip archive.
     * 
     * @param  integer $id
     * @return void
     */
    public function export($id = null)
    {
        if (null !== $id) {                     
            
            $experimentModel = new Model_Experiment($this->getConfiguration('model'));
            $sessionModel = new Model_Session($this->getConfiguration('model'));
            
            $experiment = $experimentModel->getExperimentById($id);            
            $identifier = $experiment['identifier'];
            
            $name = $experiment['name'];
            
            $view = $this->getView();
                   
            $fieldNames = $sessionModel->getColumnNames();
            array_pop($fieldNames);

            $questions = $sessionModel->getDistinctQuestionsByExperiment($experiment['identifier']);

            if ($questions->num_rows > 0) {
                while ($row = $questions->fetch_object()){

                    $keys = [];
                    $line = [];

                    $sessions = $sessionModel->getSessionsByExperimentAndQuestion($identifier, $row->question);

                    while ($session = $sessions->fetch_object()){                
                        $data = json_decode($session->data, true, 3); 
                        if (null === $data) {
                            $this->error(500, 'The JSON data wasn\'t properly coded');
                        }
                        foreach($fieldNames as $i => $j) {
                            $fieldValue[] = $session->$fieldNames[$i];
                        }
                        if (is_array($data)) {
                            $transposed = $this->__transposeArray($data, $session->question);                    
                            if (empty($keys)) {
                                $keys[] = array_merge($fieldNames, array_keys($transposed));
                            }                    
                            foreach ($data as $key => $value) {                      
                                $line[] = array_merge($fieldValue, (array) array_values($value));
                            }
                        }
                        $fieldValue = [];
                    }
                    $csv[$row->question] = array_merge($keys, $line);
                }            
                $view->file = $this->__addToArchive($name, $csv);
            } else {
                $this->redirect('index');
            }
        } else {
            $this->error(500, 'Unable to export the experiment data.');
        }

    }
    
    /**
     * Exit the application and logout
     * 
     * @return void 
     */
    public function logout()
    {
        $session = new Base_Session();
        $session->destroy();
        
        $this->redirect('index');
    }
    
    /**
     * Add data to a zip archive
     * 
     * @internal 
     * @param  string $name
     * @param  array  $data
     * @return string
     */
    private function __addToArchive($name, $data) 
    {   
        if (!class_exists('ZipArchive')) {
           $this->error(500, 'ZipArchive is not installed.'); 
        }        
        $zip = new ZipArchive();         
        if (false === $zip->open($name . '.zip', ZipArchive::CREATE)) {
            $this->error(500, 'Unable to create an archive.');
        } else {
            foreach($data as $key => $question) {
                $str = '';
                foreach($question as $line) {
                    $str .= $this->__fputcsv($line);
                }
                $zip->addFromString($key . '.csv', $str);
            }
            $zip->close();
        }        
        return $name . '.zip';
    }
    
    /**
     * Transpose an array
     * 
     * @internal 
     * @param  array $array
     * @param  string $prefix
     * @return array
     */
    private function __transposeArray($array, $prefix = null) 
    {   
        if (!is_array($array)) {
            return [];
        }        
        $list = [];
        foreach ($array as $key => $line) {
            if (is_string($key)) {
                $key = $prefix . '_' . $key;
            }
            foreach ($line as $linekey => $linevalue) {
                if (is_string($linekey)) {
                    $linekey = $prefix . '_' . $linekey;
                }
                $list[$linekey][$key] = $linevalue;
            }
        }
        return $list;
    }
    
    /**
     * Write csv line to string
     * 
     * @internal 
     * @param  array  $fields
     * @param  string $delimiter
     * @param  string $enclosure
     * @return string
     */
    private function __fputcsv($fields = array(), $delimiter = ',', $enclosure = '"') 
    {
        $str = '';
        $escape_char = '\\';
        foreach ($fields as $value) {
            if (strpos($value, $delimiter) !== false ||
                strpos($value, $enclosure) !== false ||
                strpos($value, "\n") !== false ||
                strpos($value, "\r") !== false ||
                strpos($value, "\t") !== false ||
                strpos($value, ' ') !== false) {
                $str2 = $enclosure;
                $escaped = 0;
                $len = strlen($value);
                for ($i=0; $i<$len; $i++) {
                    if ($value[$i] == $escape_char) {
                        $escaped = 1;
                    } else if (!$escaped && $value[$i] == $enclosure) {
                        $str2 .= $enclosure;
                    } else {
                        $escaped = 0;
                    }
                    $str2 .= $value[$i];
                }
                $str2 .= $enclosure;
                $str .= $str2 . $delimiter;
            } else {
                $str .= $value . $delimiter;
            }
        }
        $str = substr($str,0,-1);
        $str .= "\n";
        return $str;
    }
	
}
