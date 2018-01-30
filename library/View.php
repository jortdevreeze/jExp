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
* | View.php                                                                  |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * Loading the templates and setting the content within the template
 */
class Base_View 
{

    private $__controller = null;
    private $__action = null;
    
    private $__data = array();
    private $__path = array();
        
    /*
     * Constructor
     * 
     * @param  string $controller
     * @param  string $action
     * @return void
     */
    public function __construct($controller, $action)    
    {    
        $this->__controller = $controller;
        $this->__action = $action;
    }
    
    /*
     * Return null is a variable is requested
     *
     * @param  string $key
     * @return null
     */
    public function __get($key) 
    {
        if (array_key_exists($key, $this->__data)) {
            return $this->__data[$key];
        }
        return null;
    }
	
    /**
     * Directly assigns a variable to the view script.
     *
     * Checks first to ensure that the caller is not attempting to set a
     * protected or private member by checking for a prefixed underscore
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value) 
    {
        $this->__data[$key] = $value;
    }
	
    /**
     * Overloading to determine if a view variable is set
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key) 
    {
        if (array_key_exists($key, $this->__data)) {
            return true;
        }
        return false;
    }
    
    /*
     * Output the data in the template
     * 
     * @param  string $name
     * @return void
     */
    public function render()
    {
        /**
         * Start the output buffer
         */
        ob_start();		

        /**
         * Store the template file in the the output buffer
         */
        $this->run($this->loadTemplate());

        /**
         * Return the output buffer
         */
        return ob_get_clean();
    }
    
    /*
     * Output the error template
     * 
     * @param  string $name
     * @return void
     */
    public function error()
    {
        ob_start();		
        $this->run($this->loadTemplate(false));
        return ob_get_clean();
    }
    
    /*
     * Helper to generate links
     * 
     * @param  string $action
     * @param  array  $args
     * @return void
     */
    public function link($action, array $args = [])
    {   
        if(is_string($action)) {
            $base = $this->__controller->basePath() . $action . '/';
            if (!empty($args)) {
                foreach($args as $key => $value) {
                    $param[] = $key . '/' . $value;                   
                }
                $params = implode('/', $param) . '/';
            } else {
                $params = '';
            }
            return $base . $params;
        }
        throw new Exception("The action name should be a string.");
    }
        
    /*
     * Load the requested template
     * 
     * @return void
     */
    public function loadTemplate($includeController = true)
    {
        if (false === $includeController) {
            $path = V_PATH;
        } else {
            $name = explode('_', get_class($this->__controller));
            $path = V_PATH . lcfirst(end($name));
        }
        
        if (is_dir($path)) {
            $file = $path . '/' . $this->__action . '.php';
            if ($fh = @fopen($file, 'r', true)) {
                return $file;
            } 
            throw new Exception("Your template file '" . $file . "' does not appear to exist.");
        } 
        throw new Exception("Your template folder  '" . $path . "' does not appear to exist.");
    }
    
    /**
     * Set view directory
     *
     * @param  string $path
     * @return object
    */
    public function setPublicPath($path) 
    {
        $this->__path['images']  = $path . 'images/';
        $this->__path['styles']  = $path . 'styles/';
        $this->__path['scripts'] = $path . 'js/';
       	
        return $this;
    }
    
    /**
     * Retrieve requested path
     *
     * @param  string $type
     * @return string
     */
    public function getPublicPath($type = null) {
        switch ($type) {
            case 'images':
                $path = $this->__path[$type];
                break;
            case 'styles':
                $path = $this->__path[$type];
                break;
            case 'scripts':
                $path = $this->__path[$type];
                break;
            default:
                throw new Exception('Unknown path type is requested');
        }
        return $path;        
    }
    
    

    /**
     * Includes the view script in a scope with only public variables.
     *
     * @return void
     */
    protected function run() 
    {		
        while(list($key, $value) = each($this)) ${$key} = $value;
        include func_get_arg(0);
    }
}
