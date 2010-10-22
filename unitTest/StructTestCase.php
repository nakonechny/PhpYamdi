<?php
/**
 * Simpletest test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

require_once dirname(__FILE__).'/../autoload.php';

class StructTestCase extends UnitTestCase
{
	public function testUnpackPack()
	{
		$test_struct = new TestStruct();
		$input = 'AB';
		$test_struct->unpack($input);
		$this->assertEqual(65, $test_struct->char1);
		$this->assertEqual(66, $test_struct->char2);
		$output = $test_struct->pack();
		$this->assertEqual($input, $output);
	}
}

class TestStruct extends Yamdi_Struct
{
	protected $dataTypes = array(
		'char1' => 'C',
		'char2' => 'C'
	);
}
