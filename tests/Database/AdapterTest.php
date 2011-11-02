<?php

define('PHPDB_ROOT', realpath(dirname(__FILE__).'/../../src/').'/');
require_once PHPDB_ROOT.'Database/Adapter.php';

abstract class Flux_Database_AdapterTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Flux_Database_Adapter
	 */
	protected $db;

	public function setUp()
	{
		$this->db = $this->createAdapter();
	}

	/**
	 * @return Flux_Database_Adapter
	 */
	abstract public function createAdapter();

	public function testCreateAndRemoveTable()
	{
		$q1 = $this->db->createTable('test1');
		$q1->field('id', Flux_Database_Query_Helper_TableColumn::TYPE_SERIAL);
		$q1->run();

		$q2 = $this->db->tableExists('test1');
		$r2 = $q2->run();
		$this->assertTrue($r2);

		$q3 = $this->db->dropTable('test1');
		$q3->run();

		$r4 = $q2->run();
		$this->assertFalse($r4);
	}

	public function testFillAndEmptyTable()
	{
		$q1 = $this->db->createTable('test1');
		$q1->field('id', Flux_Database_Query_Helper_TableColumn::TYPE_SERIAL);
		$q1->field('number', Flux_Database_Query_Helper_TableColumn::TYPE_INT);
		$q1->run();

		$q2 = $this->db->insert(array('number' => ':num'), 'test1');
		$params = array(':num' => 4);
		$r2 = $q2->run($params);
		$this->assertEquals(1, $r2);

		$q3 = $this->db->select(array('number'), 'test1');
		$r3 = $q3->run();
		$this->assertEquals(1, count($r3));
		$this->assertEquals(4, $r3[0]['number']);

		$q4 = $this->db->truncate('test1');
		$q4->run();

		$r5 = $q3->run();
		$this->assertEquals(0, count($r5));

		$q6 = $this->db->dropTable('test1');
		$q6->run();
	}
}