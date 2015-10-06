<?php //-->
/*
 * This file is part of the Collection package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

class EdenSqlIndexTest extends PHPUnit_Framework_TestCase
{
    public function setup() {
        $sql = 'CREATE TABLE IF NOT EXISTS test (
            test_id INT AUTO_INCREMENT,
            test_name VARCHAR(255),
            test_text TEXT(1000),
            PRIMARY KEY (test_id))';

	    $db = eden('mysql',
			'localhost',
			'eden',
			'root',
			'');
        
        $db->query($sql);

        return $db;
    }

    public function insert() {
        $setting = array(
            'test_name' => 'name',
            'test_text' => 'Long text');

        $db = $this->setup();
        $db->insertRow('test', $setting);

        return $db;
    }

    public function testSelect() {
        $db = $this->setup();
        $select = $db->Select('*');
        $this->assertInstanceOf('Eden\\Sql\\Select', $select);
    }

    public function testSearch() {
        $db = $this->insert();
        $search = $db->search('test')
            ->getRows();

        $this->assertTrue(isset($search));
        
        $db->query('DELETE FROM test');
    }

    public function testUpdate() {
        $db = $this->insert();
        $setting = array(
            'test_name' => 'boom',
            'test_text' => 'this is a long text');

        $db->updateRows('test', $setting, array(array('test_name=%s', 'name')));
        $search = $db->search('test')
            ->setColumns('test_name', 'test_text')
            ->getRow();

        $this->assertEquals($search, $setting);
        
        $db->query('DELETE FROM test');
    }
}
