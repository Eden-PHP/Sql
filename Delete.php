<?php //-->
/*
 * This file is part of the Sql package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Sql;

/**
 * Generates delete query string syntax
 *
 * @vendor Eden
 * @package Sql
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Delete extends Query 
{
	protected $table = null;
	protected $where = array();
	
	/**
	 * Construct: Set the table, if any
	 *
	 * @param string|null
	 */
	public function __construct($table = null) 
	{
		//Argument 1 must be a string or null
		Argument::i()->test(1, 'string', 'null');
		
		if(is_string($table)) {
			$this->setTable($table);
		}
	}
	
	/**
	 * Returns the string version of the query 
	 *
	 * @return string
	 */
	public function getQuery() 
	{
		return 'DELETE FROM '
			.$this->table.' WHERE '
			.implode(' AND ', $this->where).';';
	}
	
	/**
	 * Set the table name in which you want to delete from
	 *
	 * @param string name
	 * @return Eden\Sql\Delete
	 */
	public function setTable($table) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Where clause
	 *
	 * @param array|string where
	 * @return Eden\Sql\Delete
	 */
	public function where($where) 
	{
		//Argument 1 must be a string or array
		Argument::i()->test(1, 'string', 'array');
		
		if(is_string($where)) {
			$where = array($where);
		}
		
		$this->where = array_merge($this->where, $where); 
		
		return $this;
	}	
}