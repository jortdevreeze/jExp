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
* | User.php                                                                  |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * User controller
 */
class Controller_User extends Base_Controller
{
    
    /**
     * View all users
     * 
     * @return void 
     */
    public function index()
    {
        $userModel = new Model_User($this->getConfiguration('model'));
        $users = $userModel->getAllUsers();
        
        $view = $this->getView();
        $view->users = $users;
    }
    
    /**
     * Add a new user
     * 
     * @return void 
     */
    public function add()
    {
        if (false == $this->isPost()) {
            $view = $this->getView();
        } else {
            
            $surname = filter_input(INPUT_POST, 'surname');
            $name = filter_input(INPUT_POST, 'name');
            $email = filter_input(INPUT_POST, 'email');
            $password1 = filter_input(INPUT_POST, 'password1');
            $password2 = filter_input(INPUT_POST, 'password2');
            
            if (null !== $surname && 
                null !== $name && 
                null !== $email && 
                null !== $password1 && 
                null !== $password2) {
                
                if ($password1 !== $password2) {
                    $this->error(500, 'The password you provided is wrong.');
                }
                
                $userModel = new Model_User($this->getConfiguration('model'));
                $userModel->addUser($surname, $name, $email, $password1);
            }
            $this->redirect('user');
        }
    }
    
    /**
     * Edit an existing user
     * 
     * @param  integer $id
     * @return void 
     */
    public function edit($id = null)
    {
        if (false == $this->isPost()) {            
            if (null !== $id) {
                $userModel = new Model_User($this->getConfiguration('model'));
                $view = $this->getView();
                $view->user = $userModel->getUserById($id);
            }            
        } else {
            
            $surname = filter_input(INPUT_POST, 'surname');
            $name = filter_input(INPUT_POST, 'name');
            $email = filter_input(INPUT_POST, 'email');
            $password1 = filter_input(INPUT_POST, 'password1');
            $password2 = filter_input(INPUT_POST, 'password2');
            
            if (null !== $id &&
                null !== $surname && 
                null !== $name && 
                null !== $email) {
                
                if ($password1 !== $password2) {
                    $this->error(500, 'The password you provided is wrong.');
                }
                
                $userModel = new Model_User($this->getConfiguration('model'));
                $userModel->updateUserById($id, $surname, $name, $email, $password1);
            }
            $this->redirect('user');
        }
    }
    
    /**
     * Delete an existing user
     * 
     * @param  integer $id
     * @return void 
     */
    public function delete($id = null)
    {
        if (null !== $id) {
            $userModel = new Model_User($this->getConfiguration('model'));
            $userModel->deleteUserById($id);          
        }
        $this->redirect('user');
    }

}