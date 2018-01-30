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
* | Login.php                                                                 |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * Login controller
 */
class Controller_Login extends Base_Controller
{ 
    
    public function index()
    {
        if (false == $this->isPost()) {
            $view = $this->getView();
        } else {
            
            $username = filter_input(INPUT_POST, 'username');
            $password = filter_input(INPUT_POST, 'password');
            
            if (null !== $username && null !== $password) {
                
                $userModel = new Model_User($this->getConfiguration('model'));
                
                if (true === $userModel->hasCredentials($username, $password, false)) {
                    
                    $session = new Base_Session();
                    $session->credentials = true;
                    $session->username = $username;
                    
                    $this->redirect('index');
                }
            }
            $this->error(403, 'You provided invalid credentials.');
        }
    }
    
    public function error()
    {
        
    }

}
