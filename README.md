![logo](http://eden.openovate.com/assets/images/cloud-social.png) Eden Sql
====
[![Build Status](https://api.travis-ci.org/Eden-PHP/Sql.svg)](https://travis-ci.org/Eden-PHP/Sql)
====

 - [Install](#install)
 - [Introduction](#intro)
 - [API](#api)
    - [bind](#bind)
    - [collection](#collection)
    - [delete](#delete)
    - [deleteRows](#deleteRows)
    - [getBinds](#getBinds)
    - [getConnection](#getConnection)
    - [getLastInsertedId](#getLastInsertedId)
    - [getModel](#getModel)
    - [getQueries](#getQueries)
    - [getRow](#getRow)
    - [insert](#insert)
    - [insertRow](#insertRow)
    - [insertRows](#insertRows)
    - [model](#model)
    - [query](#query)
    - [search](#search)
    - [select](#select)
    - [setBinds](#setBinds)
    - [setCollection](#setCollection)
    - [setModel](#setModel)
    - [setRow](#setRow)
    - [update](#update)
    - [updateRows](#updateRows)
 - [Contributing](#contributing)

====

<a name="install"></a>
## Install

`composer install eden/sql`

====

<a name="intro"></a>
## Introduction

SQL is an abstract package used in:

 - [MySQL](https://github.com/Eden-PHP/Mysql)
 - [PostGre](https://github.com/Eden-PHP/Postgre)
 - [SQLite](Sqlite)

See these documentations for more details. The following API methods are common amongst all SQL type databases.

====

<a name="api"></a>
## API

==== 

<a name="bind"></a>

### bind

Binds a value and returns the bound key 

#### Usage

```
eden('sql')->bind(*string|array|number|null $value);
```

#### Parameters

 - `*string|array|number|null $value` - What to bind

Returns `string`

#### Example

```
eden('sql')->bind('foo');
```

==== 

<a name="collection"></a>

### collection

Returns collection 

#### Usage

```
eden('sql')->collection(array $data);
```

#### Parameters

 - `array $data` - Initial collection data

Returns `Eden\Sql\Collection`

#### Example

```
eden('sql')->collection();
```

==== 

<a name="delete"></a>

### delete

Returns the delete query builder 

#### Usage

```
eden('sql')->delete(*string|null $table);
```

#### Parameters

 - `*string|null $table` - The table name

Returns `Eden\Sql\Delete`

#### Example

```
eden('sql')->delete('foo');
```

==== 

<a name="deleteRows"></a>

### deleteRows

Removes rows that match a filter 

#### Usage

```
eden('sql')->deleteRows(*string|null $table, array|string $filters);
```

#### Parameters

 - `*string|null $table` - The table name
 - `array|string $filters` - Filters to test against

Returns `Eden\Sql\Collection`

#### Example

```
eden('sql')->deleteRows('foo');
```

==== 

<a name="getBinds"></a>

### getBinds

Returns all the bound values of this query 

#### Usage

```
eden('sql')->getBinds();
```

#### Parameters

Returns `array`

==== 

<a name="getConnection"></a>

### getConnection

Returns the connection object if no connection has been made it will attempt to make it 

#### Usage

```
eden('sql')->getConnection();
```

#### Parameters

Returns `resource` - PDO connection resource

==== 

<a name="getLastInsertedId"></a>

### getLastInsertedId

Returns the last inserted id 

#### Usage

```
eden('sql')->getLastInsertedId(string|null $column);
```

#### Parameters

 - `string|null $column` - A particular column name

Returns `int` - the id

#### Example

```
eden('sql')->getLastInsertedId();
```

==== 

<a name="getModel"></a>

### getModel

Returns a model given the column name and the value 

#### Usage

```
eden('sql')->getModel(*string $table, *string $name, *scalar|null $value);
```

#### Parameters

 - `*string $table` - Table name
 - `*string $name` - Column name
 - `*scalar|null $value` - Column value

Returns `Eden\Sql\Model|null`

#### Example

```
eden('sql')->getModel('foo', 'foo', $value);
```

==== 

<a name="getQueries"></a>

### getQueries

Returns the history of queries made still in memory 

#### Usage

```
eden('sql')->getQueries(int|string|null $index);
```

#### Parameters

 - `int|string|null $index` - A particular index to return

Returns `array|null` - the queries

#### Example

```
eden('sql')->getQueries();
```

==== 

<a name="getRow"></a>

### getRow

Returns a 1 row result given the column name and the value 

#### Usage

```
eden('sql')->getRow(*string $table, *string $name, *scalar|null $value);
```

#### Parameters

 - `*string $table` - Table name
 - `*string $name` - Column name
 - `*scalar|null $value` - Column value

Returns `array|null`

#### Example

```
eden('sql')->getRow('foo', 'foo', $value);
```

==== 

<a name="insert"></a>

### insert

Returns the insert query builder 

#### Usage

```
eden('sql')->insert(string|null $table);
```

#### Parameters

 - `string|null $table` - Name of table

Returns `Eden\Sql\Insert`

#### Example

```
eden('sql')->insert();
```

==== 

<a name="insertRow"></a>

### insertRow

Inserts data into a table and returns the ID 

#### Usage

```
eden('sql')->insertRow(*string $table, *array $setting, bool|array $bind);
```

#### Parameters

 - `*string $table` - Table name
 - `*array $setting` - Key/value array matching table columns
 - `bool|array $bind` - Whether to compute with binded variables

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->insertRow('foo', array('foo' => 'bar'));
```

==== 

<a name="insertRows"></a>

### insertRows

Inserts multiple rows into a table 

#### Usage

```
eden('sql')->insertRows(*string $table, array $setting, bool|array $bind);
```

#### Parameters

 - `*string $table` - Table name
 - `array $setting` - Key/value 2D array matching table columns
 - `bool|array $bind` - Whether to compute with binded variables

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->insertRows('foo');
```

==== 

<a name="model"></a>

### model

Returns model 

#### Usage

```
eden('sql')->model(array $data);
```

#### Parameters

 - `array $data` - The initial data to set

Returns `Eden\Sql\Model`

#### Example

```
eden('sql')->model();
```

==== 

<a name="query"></a>

### query

Queries the database 

#### Usage

```
eden('sql')->query(*string $query, array $binds);
```

#### Parameters

 - `*string $query` - The query to ran
 - `array $binds` - List of binded values

Returns `array`

#### Example

```
eden('sql')->query('foo');
```

==== 

<a name="search"></a>

### search

Returns search 

#### Usage

```
eden('sql')->search(string|null $table);
```

#### Parameters

 - `string|null $table` - Table name

Returns `Eden\Sql\Search`

#### Example

```
eden('sql')->search();
```

==== 

<a name="select"></a>

### select

Returns the select query builder 

#### Usage

```
eden('sql')->select(string|array $select);
```

#### Parameters

 - `string|array $select` - Column list

Returns `Eden\Sql\Select`

#### Example

```
eden('sql')->select();
```

==== 

<a name="setBinds"></a>

### setBinds

Sets all the bound values of this query 

#### Usage

```
eden('sql')->setBinds(*array $binds);
```

#### Parameters

 - `*array $binds` - key/values to bind

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->setBinds(array('foo' => 'bar'));
```

==== 

<a name="setCollection"></a>

### setCollection

Sets default collection 

#### Usage

```
eden('sql')->setCollection(*string $collection);
```

#### Parameters

 - `*string $collection` - Collection class name

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->setCollection('foo');
```

==== 

<a name="setModel"></a>

### setModel

Sets the default model 

#### Usage

```
eden('sql')->setModel(*string Model);
```

#### Parameters

 - `*string Model` - class name

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->setModel('foo');
```

==== 

<a name="setRow"></a>

### setRow

Sets only 1 row given the column name and the value 

#### Usage

```
eden('sql')->setRow(*string $table, *string $name, *scalar|null $value, *array $setting);
```

#### Parameters

 - `*string $table` - Table name
 - `*string $name` - Column name
 - `*scalar|null $value` - Column value
 - `*array $setting` - Key/value array matching table columns

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->setRow('foo', 'foo', $value, array('foo' => 'bar'));
```

==== 

<a name="update"></a>

### update

Returns the update query builder 

#### Usage

```
eden('sql')->update(string|null $table);
```

#### Parameters

 - `string|null $table` - Name of table

Returns `Eden\Sql\Update`

#### Example

```
eden('sql')->update();
```

==== 

<a name="updateRows"></a>

### updateRows

Updates rows that match a filter given the update settings 

#### Usage

```
eden('sql')->updateRows(*string $table, *array $setting, array|string $filters, bool|array $bind);
```

#### Parameters

 - `*string $table` - Table name
 - `*array $setting` - Key/value array matching table columns
 - `array|string $filters` - Filters to test against
 - `bool|array $bind` - Whether to compute with binded variables

Returns `Eden\Sql\Index`

#### Example

```
eden('sql')->updateRows('foo', array('foo' => 'bar'));
```

==== 

<a name="contributing"></a>
#Contributing to Eden

Contributions to *Eden* are following the Github work flow. Please read up before contributing.

##Setting up your machine with the Eden repository and your fork

1. Fork the repository
2. Fire up your local terminal create a new branch from the `v4` branch of your 
fork with a branch name describing what your changes are. 
 Possible branch name types:
    - bugfix
    - feature
    - improvement
3. Make your changes. Always make sure to sign-off (-s) on all commits made (git commit -s -m "Commit message")

##Making pull requests

1. Please ensure to run `phpunit` before making a pull request.
2. Push your code to your remote forked version.
3. Go back to your forked version on GitHub and submit a pull request.
4. An Eden developer will review your code and merge it in when it has been classified as suitable.