<?php
/**
 * Phpunit test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

include_once dirname(__FILE__).'/setup.php';

class StructTest extends PHPUnit_Framework_TestCase
{
	public function testUnpackPackTwoChars()
	{
		$test_struct = new TwoCharsStructure();
		$input = 'AB';
		$test_struct->unpack($input);
		$this->assertEquals(65, $test_struct->char1);
		$this->assertEquals(66, $test_struct->char2);
		$output = $test_struct->pack();
		$this->assertEquals($input, $output);
	}
}

class TwoCharsStructure extends Yamdi_Struct
{
	protected $dataTypes = array(
		'char1' => 'C',
		'char2' => 'C'
	);
}
