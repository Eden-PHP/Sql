<?php //-->
/*
 * This file is part of the Sql package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Sql;

use Eden\Core\Event as CoreEvent;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a database. This class also lays out 
 * query building methods that auto renders a valid query
 * the specific database will understand without actually 
 * needing to know the query language.
 *
 * @vendor Eden
 * @package Sql
 * @author Christian Blanquera cblanquera@openovate.com
 */
abstract class Factory extends CoreEvent 
{
	const INSTANCE = 0;
	
	const FIRST = 'first';
	const LAST	= 'last';
	const QUERY = 'Eden\\Sql\\Query';
	const MODEL = 'Eden\\Sql\\Model';
	const COLLECTION = 'Eden\\Sql\\Collection';
	
	protected $queries = array();
	protected $connection = null;
	protected $binds = array();
	
	protected $model = self::MODEL;
	protected $collection = self::COLLECTION;
	
	/**
	 * Connects to the database
	 * 
	 * @param array the connection options
	 * @return Eden\Sql\Factory
	 */
	abstract public function connect(array $options = array());
	
	/**
	 * Binds a value and returns the bound key
	 *
	 * @param string|array|number|null
	 * @return string
	 */
	public function bind($value) 
	{
		//Argument 1 must be an array, string or number
		Argument::i()->test(1, 'array', 'string', 'numeric', 'null');
		
		if(is_array($value)) {
			foreach($value as $i => $item) {
				$value[$i] = $this->bind($item);
			}
			
			return '('.implode(",",$value).')';
		} else if(is_int($value) || ctype_digit($value)) {
			return $value;
		}
		
		$name = ':bind'.count($this->binds).'bind';
		$this->binds[$name] = $value;
		return $name;
	}
	
	/**
	 * Returns collection
	 *
	 * @param array
	 * @return Eden\Sql\Collection
	 */
	public function collection(array $data = array()) 
	{
		$collection = trim(str_replace('\\', '_', $this->collection), '_');
		
		return $this->$collection()
			->setDatabase($this)
			->setModel($this->model)
			->set($data);
	}
	
	/**
	 * Returns the delete query builder
	 *
	 * @param string|null
	 * @return Eden\Sql\Delete
	 */ 
	public function delete($table = null) 
	{
		//Argument 1 must be a string or null
		Argument::i()->test(1, 'string', 'null');
		
		return Delete::i($table);
	}
	
	/**
	 * Removes rows that match a filter
	 *
	 * @param string table
	 * @param array filter
	 * @return Eden\Sql\Collection
	 */
	public function deleteRows($table, $filters = null) 
	{
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string') 			
			//Argument 2 must be a string or array
			->test(2, 'array', 'string');	
		
		//clear binds
		$this->clearBinds();

		// build query
		$query = $this->delete($table);
		
		//array('post_id=%s AND post_title IN %s', 123, array('asd'));
		if(is_array($filters)) {
			//can be array of arrays
			if(is_array($filters[0])) {
				foreach($filters as $i => $filter) {
					if(is_array($filters)) {
						$format = array_shift($filter);
						
						//reindex filters
						$filter = array_values($filter);
						
						//bind filters
						foreach($filter as $i => $value) {
							$filter[$i] = $this->bind($value);
						}
						
						//combine
						$query->where(vsprintf($format, $filter));
					}
				}
			} else {
				$format = array_shift($filters);
				
				//reindex filters
				$filters = array_values($filters);
				
				//bind filters
				foreach($filters as $i => $value) {
					$filters[$i] = $this->bind($value);
				}
				
				//combine
				$query->where(vsprintf($format, $filters));
			}
		} else {
			$query->where($filters);
		}
		
		//run the query
		$this->query($query, $this->getBinds());	
		
		//trigger event
		$this->trigger('sql-delete', $table, $filters);
		
		return $this;
	}
	
	/**
	 * Returns all the bound values of this query
	 *
	 * @return array
	 */
	public function getBinds() 
	{
		return $this->binds;
	}

	/**
	 * Clears all the binded values
	 *
	 * @return array
	 */
	public function clearBinds() 
	{
		$this->binds = array();
	}
	
	/**
	 * Returns the connection object
	 * if no connection has been made 
	 * it will attempt to make it
	 *
	 * @return resource PDO connection resource
	 */
	public function getConnection() 
	{
		if(!$this->connection) {
			$this->connect();
		}
		
		return $this->connection;
	}
	
	/**
	 * Returns the last inserted id
	 *
	 * @return int the id
	 */
	public function getLastInsertedId($column = NULL) 
	{
		if(is_string($column)) {
			return $this->getConnection()->lastInsertId($column);
		}
		
		return $this->getConnection()->lastInsertId();
	}
	
	/**
	 * Returns a model given the column name and the value
	 *
	 * @param string table
	 * @param string name
	 * @param scalar|null value
	 * @return Eden\Sql\Model|null
	 */
	public function getModel($table, $name, $value) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')				
			//Argument 2 must be a string
			->test(2, 'string')				
			//Argument 3 must be scalar or null
			->test(3, 'scalar', 'null');	

		//get the row
		$result = $this->getRow($table, $name, $value);
		
		if(is_null($result)) {
			return null;
		}
		
		return $this->model()->setTable($table)->set($result);
	}
	
	/**
	 * Returns the history of queries made still in memory
	 * 
	 * @param int|string|null
	 * @return array|null the queries
	 */
	public function getQueries($index = null) 
	{
		//Argument 1 must be an int string or null
		Argument::i()->test(1, 'int', 'string', 'null');
		
		//if no index
		if(is_null($index)) {
			//return all the queries
			return $this->queries;
		}
		
		//is it the first?
		if($index == self::FIRST) {
			//index is 0 then
			$index = 0;
		//is it the last?
		} else if($index == self::LAST) {
			//set index to the last one
			$index = count($this->queries) - 1;
		}
		
		//if we have that record
		if(isset($this->queries[$index])) {
			//return it
			return $this->queries[$index];
		}
		
		return null;
	}
	
	/**
	 * Returns a 1 row result given the column name and the value
	 *
	 * @param string table
	 * @param string name
	 * @param string value
	 * @return array|null
	 */
	public function getRow($table, $name, $value) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')				
			//Argument 2 must be a string
			->test(2, 'string')				
			//Argument 3 must be scalar or null
			->test(3, 'scalar', 'null');	
		
		//clear binds
		$this->clearBinds();

		//make the query
		$query = $this->select()
			->from($table)
			->where($name.' = '.$this->bind($value))
			->limit(0, 1);
		
		//get the results
		$results = $this->query($query, $this->getBinds());
		
		//event trigger
		$this->trigger('sql-row', $table, $name, $value, $results);
		
		//if we have results
		if(isset($results[0])) {
			//return it
			return $results[0];
		}
		
		return null;
	}
	
	/**
	 * Returns the insert query builder
	 *
	 * @param string|null
	 * @return Eden\Sql\Insert
	 */ 
	public function insert($table = null) 
	{
		//Argument 1 must be a string or null
		Argument::i()->test(1, 'string', 'null');
		
		return Insert::i($table);
	}
	
	/**
	 * Inserts data into a table and returns the ID
	 *
	 * @param string table
	 * @param array setting
	 * @param bool|array
	 * @return Eden\Sql\Factory
	 */
	public function insertRow($table, array $settings, $bind = true) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')   		
			//Argument 3 must be an array or bool
			->test(3, 'array', 'bool');	
		
		//clear binds
		$this->clearBinds();

		//build insert query
		$query = $this->insert($table);
		
		//foreach settings
		foreach($settings as $key => $value) {
			//if value is not a vulnerability
			if(is_null($value) || is_bool($value)) {
				//just add it to the query
				$query->set($key, $value);
				continue;
			}
			
			//if bind is true or is an array and we want to bind it
			if($bind === true || (is_array($bind) && in_array($key, $bind))) {
				//bind the value
				$value = $this->bind($value);
			}
			
			//add it to the query
			$query->set($key, $value);
		}
		
		//run the query
		$this->query($query, $this->getBinds());	
		
		//event trigger
		$this->trigger('sql-insert', $table, $settings);
		
		return $this;
	}
	
	/**
	 * Inserts multiple rows into a table
	 *
	 * @param string table
	 * @param array settings
	 * @param bool|array
	 * @return Eden\Sql\Factory
	 */
	public function insertRows($table, array $settings, $bind = true) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')   		
			//Argument 3 must be an array or bool
			->test(3, 'array', 'bool');	
		
		//build insert query
		$query = $this->insert($table);
		
		//this is an array of arrays
		foreach($settings as $index => $setting) {
			//for each column
			foreach($setting as $key => $value) {
				//if value is not a vulnerability
				if(is_null($value) || is_bool($value)) {
					//just add it to the query
					$query->set($key, $value, $index);
					continue;
				}
				
				//if bind is true or is an array and we want to bind it
				if($bind === true || (is_array($bind) && in_array($key, $bind))) {
					//bind the value
					$value = $this->bind($value);
				}
				
				//add it to the query
				$query->set($key, $value, $index);
			}
		}
		
		//run the query
		$this->query($query, $this->getBinds());	
		
		$this->trigger('sql-inserts', $table, $settings);
		
		return $this;
	}
	
	/**
	 * Returns model
	 *
	 * @param array
	 * @return Eden\Sql\Model
	 */
	public function model(array $data = array()) 
	{
		$model = trim(str_replace('\\', '_', $this->model), '_');
		return $this->$model($data)->setDatabase($this);
	}
	
	/**
	 * Queries the database
	 * 
	 * @param string query
	 * @param array binded value
	 * @return array
	 */
	public function query($query, array $binds = array()) 
	{
		//Argument 1 must be a string or null
		Argument::i()->test(1, 'string', self::QUERY);
		
		$request = new \stdClass();
		
		$request->query = $query;
		$request->binds = $binds;
		
		//event trigger
		$this->trigger('sql-query-before', $request);
		
		$connection = $this->getConnection();
		$query 		= (string) $request->query;
		$stmt 		= $connection->prepare($query);
		
		//bind some more values
		foreach($request->binds as $key => $value) {
			$stmt->bindValue($key, $value);
		}
		
		//PDO Execute
		if(!$stmt->execute()) {
			$error = $stmt->errorInfo();
			
			//unpack binds for the report
			foreach($binds as $key => $value) {
				$query = str_replace($key, "'$value'", $query);
			}

			//clear binds
			$this->clearBinds();
			
			//throw Exception
			Exception::i()
				->setMessage(Exception::QUERY_ERROR)
				->addVariable($error[0])
				->addVariable($error[1])
				->addVariable($error[2])
				->addVariable($query)
				->trigger();
		}
		
		$results = $stmt->fetchAll( \PDO::FETCH_ASSOC );

		//clear binds
		$this->clearBinds();
		
		//log query
		$this->queries[] = array(
			'query' 	=> $query,
			'binds' 	=> $binds,
			'results' 	=> $results);

		//event trigger
		$this->trigger('sql-query-after', $query, $binds, $results);
		
		return $results;
	}
	
	/**
	 * Returns search
	 *
	 * @param string|null
	 * @return Eden\Sql\Search
	 */
	public function search($table = null) 
	{
		//Argument 1 must be a string or null
		Argument::i()->test(1, 'string', 'null');
		
		$search = Search::i($this)
			->setCollection($this->collection)
			->setModel($this->model);
		
		if($table) {
			$search->setTable($table);
		}
		
		return $search;
	}
	
	/**
	 * Returns the select query builder
	 *
	 * @param string|array
	 * @return Eden\Sql\Select
	 */ 
	public function select($select = '*') 
	{
		//Argument 1 must be a string or array
		Argument::i()->test(1, 'string', 'array');
		
		return Select::i($select);
	}
	
	/**
	 * Sets all the bound values of this query
	 *
	 * @param array
	 * @return Eden\Sql\Factory
	 */
	public function setBinds(array $binds)
	{
		$this->binds = $binds;
		return $this;
	}
	
	/**
	 * Sets default collection
	 *
	 * @param string
	 * @return Eden\Sql\Factory
	 */
	public function setCollection($collection) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		if($collection != self::COLLECTION 
		&& !is_subclass_of($collection, self::COLLECTION)) {
			Exception::i()
				->setMessage(Exception::NOT_SUB_COLLECTION)
				->addVariable($collection)
				->trigger();
		}
		
		$this->collection = $collection;
		return $this;
	}
	
	/**
	 * Sets the default model
	 *
	 * @param string
	 * @return Eden\Sql\Factory
	 */
	public function setModel($model) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		if($model != self::MODEL 
		&& !is_subclass_of($model, self::MODEL)) {
			Exception::i()
				->setMessage(Exception::NOT_SUB_MODEL)
				->addVariable($model)
				->trigger();
		}
		
		$this->model = $model;
		return $this;
	}
	
	/**
	 * Sets only 1 row given the column name and the value
	 *
	 * @param string table
	 * @param string name
	 * @param string value
	 * @param array setting
	 * @return Eden\Sql\Factory
	 */
	public function setRow($table, $name, $value, array $setting) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')				
			//Argument 2 must be a string
			->test(2, 'string')				
			//Argument 3 must be a string or number
			->test(3, 'string', 'numeric');	
		
		//first check to see if the row exists
		$row = $this->getRow($table, $name, $value);
		
		if(!$row) {
			//we need to insert
			$setting[$name] = $value;
			return $this->insertRow($table, $setting);
		} 
		
		//we need to update this row
		return $this->updateRows($table, $setting, array($name.'=%s', $value));
	}
	
	/**
	 * Returns the update query builder
	 *
	 * @return Eden\Sql\Update
	 */ 
	public function update($table = null) 
	{
		//Argument 1 must be a string or null
		Argument::i()->test(1, 'string', 'null');
		
		return Update::i($table);
	}
	
	/**
	 * Updates rows that match a filter given the update settings
	 *
	 * @param string table
	 * @param array setting
	 * @param array filter
	 * @return var
	 */
	public function updateRows($table, array $settings, $filters = null, $bind = true) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')
			//Argument 3 must be a string or array
			->test(3, 'array', 'string')
			//Argument 4 must be a string or bool
			->test(4, 'array', 'bool');
		
		//clear binds
		$this->clearBinds();

		//build the query
		$query = $this->update($table);
		
		//foreach settings
		foreach($settings as $key => $value) {
			//if value is not a vulnerability
			if(is_null($value) || is_bool($value)) {
				//just add it to the query
				$query->set($key, $value);
				continue;
			}
			
			//if bind is true or is an array and we want to bind it
			if($bind === true || (is_array($bind) && in_array($key, $bind))) {
				//bind the value
				$value = $this->bind($value);
			}
			
			//add it to the query
			$query->set($key, $value);
		}
		
		//array('post_id=%s AND post_title IN %s', 123, array('asd'));
		if(is_array($filters)) {
			//can be array of arrays
			if(is_array($filters[0])) {
				foreach($filters as $i => $filter) {
					if(is_array($filters)) {
						$format = array_shift($filter);
						
						//reindex filters
						$filter = array_values($filter);
						
						//bind filters
						foreach($filter as $i => $value) {
							$filter[$i] = $this->bind($value);
						}
						
						//combine
						$query->where(vsprintf($format, $filter));
					}
				}
			} else {
				$format = array_shift($filters);
				
				//reindex filters
				$filters = array_values($filters);
				
				//bind filters
				foreach($filters as $i => $value) {
					$filters[$i] = $this->bind($value);
				}
				
				//combine
				$query->where(vsprintf($format, $filters));
			}
		} else {
			$query->where($filters);
		}
		
		//run the query
		$this->query($query, $this->getBinds());
		
		//event trigger
		$this->trigger('sql-update', $table, $settings, $filters);
		
		return $this;
	}	
}