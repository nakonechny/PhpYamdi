<?php
/**
 * Phpunit test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

include_once dirname(__FILE__).'/setup.php';

class FlvFileHeaderTest extends PHPUnit_Framework_TestCase
{
	public function testReadWrite()
	{
		/* byte-image of a valid flv header */ 
		$bytes = 'FLV'.
			chr(1).
			chr(5).
			chr(0).chr(0).chr(0).chr(9);

		/* preparing mock streams */
		$inputStub = $this->getMock('Yamdi_InputStream', array(), array(''), '', false, false);
		$inputStub->expects($this->any())
			->method('read')
			->will($this->returnValue($bytes));
		
		$outputMock = $this->getMock('Yamdi_OutputStream', array(), array(''), '', false, false);
		$outputMock->expects($this->once())
			->method('write')
			->with($this->equalTo($bytes))
			->will($this->returnValue(strlen($bytes)));
			
		/* reading tag */
		$tag = new Yamdi_FlvFileHeader();
		$tag->read($inputStub);
		
		$this->assertEquals($tag->signature, 'FLV');
		$this->assertEquals($tag->version, 1);
		$this->assertEquals($tag->flags, 5);
		$this->assertEquals($tag->headersize, 9);
		
		/* writing tag */
		$tag->write($outputMock);
	}
}