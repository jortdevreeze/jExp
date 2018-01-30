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
* | Session.php                                                               |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

class Model_Session extends Base_Model
{
	
    public function addSession($experiment, $identifier, $question, $data)
    {   
        
        $date = new DateTime();
        $timestamp = $date->format('Y-m-d H:i:s');
        
        $error = (null === json_decode($data)) ? 1 : 0;  
        
        $result = $this->query(
            sprintf(
                "INSERT INTO %s (experiment, identifier, timestamp, question, error, data) VALUES ('%s', '%s', '%s', '%s', %b, '%s')", 
                $this->getTableName(), $experiment, $identifier, $timestamp, $question, $error, $data
            )
         );

        return $result;
    }

    public function getAllSessionsByExperiment($experiment)
    {
        $result = $this->query(
            sprintf("SELECT * FROM %s WHERE experiment='%s'", $this->getTableName(), $experiment)
         );
        
        return $result;
    }
    
    public function getSessionsByExperimentAndQuestion($experiment, $question)
    {
        $result = $this->query(
            sprintf("SELECT * FROM %s WHERE experiment='%s' AND question='%s'", $this->getTableName(), $experiment, $question)
         );
        
        return $result;
    }
    
    public function getDistinctQuestionsByExperiment($experiment)
    {
        $result = $this->query(
            sprintf("SELECT DISTINCT question FROM %s WHERE experiment='%s'", $this->getTableName(), $experiment)
         );
        
        return $result;
    }
    
    public function deleteSessionsByExperiment($experiment)
    {
        $result = $this->query(
            sprintf("DELETE FROM %s WHERE experiment='%s'", $this->getTableName(), $experiment)
         );

        return $result;
    }
    
    public function deleteAllSessions()
    {
        $result = $this->query(
            sprintf("DELETE FROM %s ", $this->getTableName())
         );
        
        return $result;
    }
	
}
