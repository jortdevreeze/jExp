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

class Model_User extends Base_Model {
    
    public function addUser($surname, $name, $email, $password, $crypt = false)
    {
        $hash = (false === $crypt) ? md5($password) : crypt($password);
        
        $result = $this->query(
            sprintf(
                "INSERT INTO %s (surname, name, email, password) VALUES ('%s', '%s', '%s', '%s')", 
                $this->getTableName(), $surname, $name, $email, $hash
            )
         );
        
        return $result;
    }
    
    public function updateUserById($id, $surname, $name, $email, $password, $crypt = false)
    {
        if (null === $password) 
        {
            $result = $this->query(
                sprintf(
                    "UPDATE %s SET surname='%s', name='%s', email='%s', WHERE id=%d", 
                    $this->getTableName(), $surname, $name, $email, $id
                )
             );
        } else 
        {
            $hash = (false === $crypt) ? md5($password) : crypt($password);
            $result = $this->query(
                sprintf(
                    "UPDATE %s SET surname='%s', name='%s', email='%s', password='%s' WHERE id=%d", 
                    $this->getTableName(), $surname, $name, $email, $hash, $id
                )
             );
        }
        return $result;
    }
    
    public function getUserById($id)
    {   
        $result = $this->query(
            sprintf("SELECT * FROM %s WHERE id=%d", $this->getTableName(), $id)
         );
        
        return (array) $result->fetch_object();
    }
    
    public function getAllUsers()
    {
        $result = $this->query(
            sprintf("SELECT * FROM %s", $this->getTableName())
         );
        
        return $result;
    }
    
    public function deleteUserById($id)
    {
        $result = $this->query(
            sprintf("DELETE FROM %s WHERE id=%d", $this->getTableName(), $id)
         );
        
        return $result;
    }
    
    public function hasCredentials($username, $password, $crypt = false)
    {   
        $result = $this->query(
            sprintf("SELECT * FROM %s WHERE email='%s'", $this->getTableName(), $username)
         );
        
        $user = $result->fetch_object();

        if (false === $crypt) {
            $hash = md5($password);
            return ($user->password === $hash)  ? true : false;
        } else {
            $hash = crypt($password);
            if(!function_exists('hash_equals')) {
                return ($this->__hashEquals($user->password, $hash)) ? true : false;
            } else {
                return (hash_equals($user->password, $hash)) ? true : false;
            }
        }        
    }      
    
    private function __hashEquals($a, $b) 
    {
        if(strlen($a) != strlen($b)) {
            return false;
        } else {
            $res = $a^$b;
            $ret = 0;
            for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
            return !$ret;
        }
    }
    
}
