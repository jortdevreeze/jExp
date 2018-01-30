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

class Base_Session {

    /**
     * @var boolean
     */
    private $__started = false;

	
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() 
    {		
        $this->start();
    }

	
    /**
     * Start the session.
     *
     * @return void
     */
    public function start() 
    {
        if (false === $this->__started) {		
            session_start();	
        }		
        $this->__started = true;
    }
	
    /**
     * Returns the current session identifier.
     *
     * @return string
     */
    public function getIdentifier() 
    {
        if (false === $this->__started) {
            throw new Exception('The session has not been started yet.');
        }
        return session_id();
    }

	
    /**
     * Explicitly destroys all session data.
     *
     * @return void
     */
    public function destroy() 
    {
        if (false === $this->__started) {
            throw new Exception('The session has not been started yet.');
        }
        session_destroy();
        $this->clean();
    }
	
    /**
     * Explicitly destroys all session data.
     *
     * @return void
     */
    public function clean() 
    {
        $_SESSION = array();
    }
    
    /**
     * Overloading for setting class property values
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value) 
    {
        $_SESSION[$key] = $value;
    }
	
    /**
     * Overloading for accessing class property values
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key) 
    {        
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }     
    }
	
    /**
     * Overloading to determine if a property is set
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key) 
    {  
        return (isset($_SESSION[$key]));
    }
	
    /**
     * Overloading to remove a property
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key) 
    {  
        unset($_SESSION[$this->_namespace][$key]);
    }
    
}