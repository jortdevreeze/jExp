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
* | Dispatcher.php                                                            |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/*
 * The main controller responsible for the application logic
 */
class Base_Dispatcher 
{
    
    private $__basePath = null;
    
    private $__prefix = 'Controller';
    private $__delimiter = '_';
    
    private $__defaultController = 'page';
    private $__loginController = 'login';
    
    private $__defaultAction = 'index';    
    private $__errorAction = 'error';

    private $__controller = null;
    private $__action = null;
    private $__params = array();
    
    private $__routes = array();
    private $__config = array();
    
    private $__error = array();
       
    /*
     * Dispatch request to the specified controller
     */
    public function dispatch() 
    {   
        /*
         * Add specified routes
         */
        $this->setRoutes($this->getConfiguration('routes')); 

        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI'); 
        
        if (strlen($uri) > 0)
        {
            $variables = array_slice(explode('/', $uri), 1);
        } else 
        {
            $variables = array();
        }
        
        if ('' === end($variables))
        {
            array_pop($variables);
        }
        
        $params = [];
       
        /*
         * Set controller and action
         */
        if (count($variables) > 0)
        {   
            $defaultController = (false === $this->getRoute($variables[0])) ? true : false;
            
            foreach ($variables as $key => $value) 
            {   
                if (true === $defaultController)
                {   
                    if ($key == 0)
                    {   
                        $this->setController(null);
                        $this->setAction($value);
                    } else
                    {   
                        $params[] = $value;
                    }
                } else                        
                {   
                    switch($key)
                    {
                        case 0:
                            $this->setController($value);
                            break;
                        case 1:
                            $this->setAction($value);
                            break;
                        default:
                            $params[] = $value;
                            break;
                    }
                }                    
            }
            if (null === $this->getAction()) 
            { 
                $this->setAction(null);
            }
        } else 
        { 
            $this->setController(null);
            $this->setAction(null);
        }

        /*
         * Set request parameters
         */
        if (count($params) > 0)
        {
            $this->setParams($params);
        }
        
        /*
         * Load the controller
         */
        $controller = $this->getController();
        
        /*
         * Add configuration to the requested controller
         */
        $config = $this->getConfiguration();
        
        if (!empty($config))
        {
            $controller->setConfiguration($config);
        }
                        
        /*
         * Call requested action
         */        
        if(!method_exists($controller, $this->getAction())) { 
          $this->__error = ['code' => 404, 'message' => 'Can\'t find the requested page.'];
          $this->setAction($this->__errorAction);
        }
        
        /**
         * Check if the user has credentials to access the requested page
         */
        if (false === $this->hasAccess($controller->getControllerName(), $this->getAction())) {
            $access = new Base_Session();
            if (null === $access->credentials || false === $access->credentials) {
                $controller->redirect(null, $this->__loginController);
            }
        }
        
        /*
         * Prepare the viewer to output the data
         */
        $controller->setView($controller, $this->getAction());
               
        
        /**
         * Dispatch the request
         */
        if (empty($this->__error))
        {
            call_user_func_array(array($controller, $this->getAction()), $this->getParams());
        } else
        {
            call_user_func_array(array($controller, $this->getAction()), array($this->__error['code'], $this->__error['message']));
        }
        
        $controller->render();
	        
    }
    
        
    /*
     * Determine if the user has access to the requested page
     * 
     * @param  string $controllerName
     * @param  string $actionName
     * @return boolean
     */
    public function hasAccess($controllerName, $actionName) 
    {   
        $hasAccess = false;
        $access = $this->getConfiguration('access');

        if(array_key_exists($controllerName, $access)) {            
            $controllerAccess = $access[$controllerName];            
            if(!is_array($controllerAccess)) {
                throw new Exception("Access for a controller should be specified in an array.");
            } else {
                if(array_key_exists('*', $controllerAccess)) {
                    $hasAccess = ('false' == $controllerAccess['*']) ? false : true;
                } else if (array_key_exists($actionName, $controllerAccess)) {
                    if(!in_array($controllerAccess, $actionName)) {
                        $hasAccess = false;
                    } else {
                        $hasAccess = ('false' == $controllerAccess[$actionName]) ? false : true;
                    }                    
                }
            }
        } else if(array_key_exists('*', $access)) {
            $hasAccess = ('false' == $access['*']) ? false : true;
        }
        
        return $hasAccess;        
    }
    
    /*
     * Configure the application
     * 
     * @param  array $values
     */
    public function setConfiguration($values)
    {
        if(is_array($values)) {
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
                return array();
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
     * Set all routes
     * 
     * @param  array $values
     */
    public function setRoutes($values)
    {
        if(is_array($values)) {
            foreach ($values as $key => $value)
            {
                $this->__routes[$key] = $value;
            }
        }
    }
    
    /*
     * Set specified routes
     * 
     * @param  array $value
     */
    public function setRoute($key, $value)
    {
        $this->__routes[$key] = $value;
    }
    
    /*
     * Get all routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->__routes;
    }
    
    /*
     * Get specified routes
     * 
     * @param  array $key
     * @return mixed
     */
    public function getRoute($key)
    {
        if (array_key_exists($key, $this->__routes)) 
        {
            return $this->__routes[$key];
        }
        return false;
    }
    
    /*
     * Set requested controller name
     * 
     * @param  string $value
     */
    public function setController($value) 
    {   
        if (null !== $value) 
        {
            $controller = $this->__prefix . $this->__delimiter . ucfirst($value);
            $this->__controller = new $controller();
            
            if (false === $this->__controller)
            {
                $this->__error = ['code' => 404, 'message' => 'Can\'t find the requested page.'];
            }
        } else
        {
            $controller = $this->__prefix . $this->__delimiter . ucfirst($this->__defaultController);                       
        }
        $this->__controller = new $controller();
    }
    
    /*
     * Get requested controller name
     * 
     * @return string
     */
    public function getController() 
    {
        return $this->__controller;
    }
    
    /*
     * Set requested action name
     * 
     * @param  string $value
     */
    public function setAction($value) 
    {
        if (null !== $value) 
        {
            $this->__action = $value;
        } else 
        {   
            $this->__action = $this->__defaultAction;           
        }
    }
    
    /*
     * Get requested action name
     * 
     * @return string
     */
    public function getAction() 
    {
        return $this->__action;
    }
    
    /*
     * Set request parameters
     * 
     * @param  array $params
     */
    public function setParams($params) 
    {
        if (count($params) > 1)
        {
            if (count($params) & 1) 
            {
                $params = array_slice($params, 1, -1);
            }
            foreach($params as $key => $value)
            {
                if ($key & 1) 
                {
                    $keys[] = $value;
                } else
                {
                    $values[] = $value;
                }
            }
            $this->__params = array_combine($values, $keys);
        }                
    }
    
    /*
     * Get request parameters
     * 
     * @return array
     */
    public function getParams() 
    {
        return $this->__params;
        
    }
    
    /*
     * Get specified request parameter
     * 
     * @param  string $key
     * @return string
     */
    public function getParam($key) 
    {
        if (array_key_exists($key, $this->__params)) 
        {
            return $this->__params[$key];
        } 
        return false;               
    }
    
    /*
     * Get the base path needed to determine the request
     * 
     * @return string
     */
    public function getBasePath() 
    { 
        if (null == $this->__basePath) 
        {
            $path = explode('/', filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
            $path = array_slice($path, 1, -1);
            $path = '/' . implode('/', $path) . '/';
            $this->__basePath = $path;
        }
        return $this->__basePath;
    }

}