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
* | Controller.php                                                            |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * The main controller responsible for the application logic
 */
class Base_Controller 
{

    private $__config = [];
    private $__params = [];
    private $__access = [];
    private $__view = null;
    private $__render = true;
    
    private $__error = [
        400 => 'HTTP/1.0 400 Bad Request', 
        403 => 'HTTP/1.0 403 Forbidden', 
        404 => 'HTTP/1.0 404 Not Found', 
        500 => 'HTTP/1.0 500 Internal Server Error'
    ];
          
    /*
     * Output all data to the view
     * 
     * @return void
     */
    public function render()    
    {
        if(true === $this->__render) 
        {
            echo $this->__view->setPublicPath($this->publicPath())->render();
        }        
    }
    
    /*
     * Do not render the output
     * 
     * @return void
     */
    public function doNotRender()    
    {
        $this->__render = false;     
    }
    
    /*
     * Default error controller
     * 
     * @param  integer $code
     * @param  string  $message
     * @return void
     */
    public function error($code, $message = '') 
    {   
        $this->doNotRender();
        
        if (!array_key_exists($code, $this->__error) || !is_integer($code)) 
        {
            $code = 500;
        }
        
        /**
         * Set HTTP header
         */
        header($this->__error[$code]);
        
        /**
         * Output error message in error template
         */
        $this->__view->message = (!empty($message)) ? $message : 'An unexpected error occurd.';
        echo $this->__view->setPublicPath($this->publicPath())->error();
    }
       
    /*
     * Configure the application
     * 
     * @param  array $values
     * @return void
     */
    public function setConfiguration($values)
    {
        if(is_array($values)) 
        {
            foreach ($values as $key => $value)
            {
                $this->__config[$key] = $value;
            }
        }
    }
    
    /*
     * Get the required configuration
     * 
     * @param  string $key
     * @return array
     */
    public function getConfiguration($key = null)
    {           
        if (null !== $key) 
        {
            if (!array_key_exists($key, $this->__config)) 
            {
                return [];
            } else 
            {
                return $this->__config[$key];
            }
        } else
        {
            return $this->__config;      
        }                
    }
    
    /*
     * Prepare the viewer to output the data
     * 
     * @param  string $controller
     * @param  string $action
     * @return void
     */
    public function setView($controller, $action)    
    {
        $this->__view = new Base_View($controller, $action);
    }
    
    /*
     * Return the viewer to output the data
     * 
     * @return object
     */
    public function getView()    
    {
        return $this->__view;       
    }
	
    /**
     * Redirect to another URL.
     *
     * @param  string  $actionName
     * @param  string  $controllerName
     * @param  array   $arguments
     * @param  integer $statusCode
     * @return void
     */
    public function redirect($actionName, $controllerName = null, array $arguments = null, $statusCode = 303)
    {
        $url = $this->publicPath($path = null);

        if (null !== $controllerName) $url .= $controllerName . '/';
        if (null !== $actionName)     $url .= $actionName . '/';

        if (null !== $arguments) 
        {
            foreach ($arguments as $key => $value) 
            {
                    $url .= $key . '/' . $value . '/';
            }
        }
        header('Location: ' . $url, true, $statusCode);
        die();
    }
    
    /*
     * Get the name for the controller
     * 
     * @return string
     */
    public function getControllerName()
    {
        $name = explode('_', get_class($this));        
        return lcfirst(end($name));
    }  
    
    /*
     * Initialize the public path which is needed by the viewer
     * 
     * @param  string $name
     * @return string
     */
    public function publicPath($name = 'public') 
    {         
        $path = explode('/', filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
        $path = array_slice($path, 0, -1);   
        $http = (false === $this->isHttp()) ? 'http://' : 'https://'; 
        $path = $http . filter_input(INPUT_SERVER, 'HTTP_HOST') . implode('/', $path);
    
        if (is_string($name)) {
            $path .= '/' . $name . '/';
        } else if (null === $name) {
            $path .= '/';
        } else {
            throw new Exception("The path name should be a string.");
        }
        return $path;
    }
    
    /*
     * Get the base path needed to determine the request
     * 
     * @return string
     */
    public function basePath() 
    { 
        $path = explode('/', filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
        $path = array_slice($path, 1, -1);
        $path = implode('/', $path) . '/';
        return $path;
    }

    /*
     * Determine if the request is HTTP or SHTTP
     * 
     * @return boolean
     */
    public function isHttp() 
    {         
        $http = filter_input(INPUT_SERVER, 'HTTPS');
        if (!empty($http)) {
            return true;
        }       
        return false;
    }
    
    /*
     * Determine if the request is HTTP or SHTTP
     * 
     * @return boolean
     */
    public function isPost() 
    {         
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if ($method == 'POST') {
            return true;
        }       
        return false;
    }
    
    /*
     * Determine if the request is HTTP or SHTTP
     * 
     * @return boolean
     */
    public function isGet() 
    {         
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if ($method == 'GET') {
            return true;
        }       
        return false;
    }
    
    /**
     * Is the request a Javascript XMLHttpRequest (AJAX)
     *
     * @return boolean
     */
    public function isAjaxRequest() {
        return ('XMLHttpRequest' === filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH'));
    }

}