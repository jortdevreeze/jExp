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
* | Model.php                                                                 |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

class Base_Model 
{
    
    private $__config = array();
    private $__error = false;
    private $__connection = null;
    private $__parts = array();
    
    /*
     * Constructor
     * 
     * @param  array $config
     * @return void
     */
    public function __construct($config = array())    
    {   
        /*
         * Save database parameters and connect to the database
         */
        if (empty($config)) {
            $this->__error = true;
        } else {
            $this->__config = $config;
            if (false === $this->connect()) {
                $this->__error = true;
            }
        }        

    }
    
    /*
     * Connect to the database
     * 
     * @return boolean
     */
    public function connect()
    {
        $this->__connection = new mysqli(
            $this->__config['host'], 
            $this->__config['username'], 
            $this->__config['password'],
            $this->__config['dbname']
        );
        
        if (!$this->__connection) {
            return false;
        }
        return true;
    }
    
    /*
     * Close the the database connection
     * 
     * @return void
     */
    public function close()
    {
        $this->__connection->close();
    }
    
    /*
     * Run SQL query
     * 
     * @return object
     */
    public function query($query)
    {
        return $this->__connection->query(strval($query));
    }
    
    /*
     * Returns a string description of the last error
     * 
     * @return string
     */
    public function error()
    {
        return $this->__connection->error;
    }
    
    
    /*
     * Validate result
     * 
     * @return boolean
     */
    public function validateResult($result)
    {
        if (false !== $result) {
            return true;
        }
        return false;
    }
        
    /*
     * Get the table name for the model
     * 
     * @return string
     */
    public function getTableName()
    {
        $name = explode('_', get_class($this));
        
        return lcfirst(
            end($name)
        );
    }   
    
    /*
     * Get the column names from the specified table name
     * 
     * @return string
     */
    public function getColumnNames()
    {
        $result = $this->query(sprintf("SHOW COLUMNS FROM %s", $this->getTableName()));
        while ($row = $result->fetch_object()){
            $columns[] = $row->Field;
        }
        return $columns;
    }
		
    /*
     * Adds new fields to be returned by a SELECT statement when this query is
     * executed.
     *
     * @param  mixed $fields
     * @return object
     */
    public function select($fields = [])
    {
        if (!is_array($fields)) {
            $fields = [$fields];           
        }
        $this->__parts['select'] = array_merge($this->__parts['select'], $fields);
        return $this;
    }
    
    /*
     * Adds a single or multiple tables to be used in the FROM clause for this query.
     * 
     * @param  mixed $tables
     * @return object
     */
    public function from($tables = [])
    {
        if (is_string($tables)) {
            $tables = [$tables];
        }
        $this->__parts['from'] = array_merge($this->__parts['select'], $fields);
        return $this;
    }
    
}