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
* | Experiment.php                                                            |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

class Model_Experiment extends Base_Model
{
       
    public function addExperiment($name, $identifier = null)
    {
        /**
         * Create md5 hash when no identifier is provided
         */
        if (null === $identifier) {
            $identifier = md5(time());
        }
        
        $result = $this->query(
            sprintf("INSERT INTO %s (name, identifier) VALUES ('%s', '%s')", $this->getTableName(), $name, $identifier)
         );
        
        return $result;
    }
    
    public function updateExperimentById($id, $name)
    {
        $result = $this->query(
            sprintf("UPDATE %s SET name='%s' WHERE id=%d", $this->getTableName(), $name, $id)
         );
        return $result;
    }

    public function getExperimentById($id)
    {   
        $result = $this->query(
            sprintf("SELECT * FROM %s WHERE id=%d", $this->getTableName(), $id)
         );
        
        return (array) $result->fetch_object();
    }

    public function getAllExperiments()
    {
        $result = $this->query(
            sprintf("SELECT * FROM %s", $this->getTableName())
         );
        
        return $result;
    }

    public function deleteExperimentById($id)
    {
        $result = $this->query(
            sprintf("DELETE FROM %s WHERE id=%d", $this->getTableName(), $id)
         );
        
        return $result;
    }
	
}
