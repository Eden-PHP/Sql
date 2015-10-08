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
 * Sql Errors
 *
 * @vendor Eden
 * @package sql
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Exception extends \Eden\Core\Exception
{
    const QUERY_ERROR       = '%s Query: %s';
    const TABLE_NOT_SET     = 'No default table set or was passed.';
    const DATABASE_NOT_SET  = 'No default database set or was passed.';
    
    const NOT_SUB_MODEL         = 'Class %s is not a child of Eden\\Model\\Index';
    const NOT_SUB_COLLECTION    = 'Class %s is not a child of Eden\\Collection\\Index';
}
