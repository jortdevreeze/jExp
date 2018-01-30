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
* | Json.php                                                                  |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * Json controller
 */
class Controller_Json extends Base_Controller
{

    public function post()
    {
        $this->doNotRender();        
        
        if (true === $this->isAjaxRequest() || true === $this->isPost()) { 
            
            $experiment = filter_input(INPUT_POST, 'experiment');
            $identifier = filter_input(INPUT_POST, 'identifier');
            $question = filter_input(INPUT_POST, 'question');
            $content = filter_input(INPUT_POST, 'content');
                        
            $sessionModel = new Model_Session($this->getConfiguration('model'));            
            $sessionModel->addSession($experiment, $identifier, $question, $content);

            return true;
        }        
        return false;        
    }
    
    public function error($code, $message = '')
    {
        $this->doNotRender(); 
        return false; 
    }
    
}
