<?php //-->
/*
 * This file is part of the Utility package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Sql;

use Eden\Utility\Collection as UtilityCollection;

/**
 * Sql Collection handler
 *
 * @vendor Eden
 * @package Sql
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Collection extends UtilityCollection 
{
	protected $_model = Database::MODEL;
	protected $_database = null;
	protected $_table = null;
	
	/**
	 * Adds a row to the collection
	 *
	 * @param array|Eden_Model
	 * @return Eden\Sql\Collection
	 */
	public function add($row = array()) 
	{
		//Argument 1 must be an array or Eden_Model
		Argument::i()->test(1, 'array', $this->_model);
		
		//if it's an array
		if(is_array($row)) {
			//make it a model
			$model = $this->_model;
			$row = $this->$model($row);
		}
		
		if(!is_null($this->_database)) {
			$row->setDatabase($this->_database);
		}
		
		if(!is_null($this->_table)) {
			$row->setTable($this->_table);
		}
		
		//add it now
		$this->_list[] = $row;
		
		return $this;
	}
	
	/**
	 * Sets the default database
	 *
	 * @param Eden\Sql\Database
	 * @return Eden\Sql\Collection
	 */
	public function setDatabase(Database $database) 
	{
		$this->_database = $database;
		
		//for each row
		foreach($this->_list as $row) {
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
		$error = Argument::i()->test(1, 'string');
		
		if(!is_subclass_of($model, 'Eden_Model')) {
			$error->setMessage(Exception::NOT_SUB_MODEL)
				->addVariable($model)
				->trigger();
		}
		
		$this->_model = $model;
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
		
		$this->_table = $table;
		
		//for each row
		foreach($this->_list as $row) {
			if(!is_object($row) || !method_exists($row, __FUNCTION__)) {
				continue;
			}
			
			//let the row handle this
			$row->setTable($table);
		}
		
		return $this;
	}	
}