<?php //-->
/*
 * This file is part of the Sql package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Sql;

use Eden\Collection\Base as CollectionBase;

/**
 * Sql Collection handler
 *
 * @vendor Eden
 * @package Sql
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Collection extends CollectionBase 
{
	protected $model = Factory::MODEL;
	protected $database = null;
	protected $table = null;
	
	/**
	 * Adds a row to the collection
	 *
	 * @param array|Eden_Model
	 * @return Eden\Sql\Collection
	 */
	public function add($row = array()) 
	{
		//Argument 1 must be an array or Eden_Model
		Argument::i()->test(1, 'array', $this->model);
		
		//if it's an array
		if(is_array($row)) {
			//make it a model
			$model = trim(str_replace('\\', '_', $this->model), '_');
			$row = $this->$model($row);
		}
		
		if(!is_null($this->database)) {
			$row->setDatabase($this->database);
		}
		
		if(!is_null($this->table)) {
			$row->setTable($this->table);
		}
		
		//add it now
		$this->list[] = $row;
		
		return $this;
	}
	
	/**
	 * Sets the default database
	 *
	 * @param Eden\Sql\Factory
	 * @return Eden\Sql\Collection
	 */
	public function setDatabase(Factory $database) 
	{
		$this->database = $database;
		
		//for each row
		foreach($this->list as $row) {
			if(!is_object($row) || !method_exists($row, __FUNCTION__)) {
				continue;
			}
			
			//let the row handle this
			$row->setDatabase($database);
		}
		
		return $this;
	}
	
	/**
	 * Sets default model
	 *
	 * @param string
	 * @return Eden\Sql\Collection
	 */
	public function setModel($model) 
	{
		Argument::i()->test(1, 'string');
		
		if($model != Factory::MODEL 
		&& !is_subclass_of($model, Factory::MODEL)) {
			Exception::i()
				->setMessage(Exception::NOT_SUB_MODEL)
				->addVariable($model)
				->trigger();
		}
		
		$this->model = $model;
		return $this;
	}
	
	/**
	 * Sets the default database
	 *
	 * @param string
	 * @return Eden\Sql\Collection
	 */
	public function setTable($table) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->table = $table;
		
		//for each row
		foreach($this->list as $row) {
			if(!is_object($row) || !method_exists($row, __FUNCTION__)) {
				continue;
			}
			
			//let the row handle this
			$row->setTable($table);
		}
		
		return $this;
	}	
}