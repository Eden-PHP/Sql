<?php //-->
/*
 * This file is part of the Utility package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Sql;

/**
 * Generates select query string syntax
 *
 * @vendor Eden
 * @package Sql
 * @author     Christian Blanquera cblanquera@openovate.com
 */
abstract class Query extends Eden_Class 
{
	/**
	 * Transform class to string using 
	 * getQuery
	 *
	 * @return string
	 */
	public function __toString() 
	{
		return $this->getQuery();
	}
	
	/**
	 * Returns the string version of the query 
	 *
	 * @param  bool
	 * @return string
	 */
	abstract public function getQuery();
}