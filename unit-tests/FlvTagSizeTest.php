<?php
/**
 * Phpunit test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

include_once dirname(__FILE__).'/setup.php';

class FlvTagSizeTest extends PHPUnit_Framework_TestCase
{
	public function testReadWrite()
	{
		$bytes = chr(0).chr(0).chr(0).chr(1); // 32-bit big-endian integer 
		
		/* preparing mock streams */
		$inputStub = $this->getMock('Yamdi_InputStream', array(), array(''), '', false, false);
		$inputStub->expects($this->any())
			->method('read')
			->will($this->returnValue($bytes));
			
		$outputMock = $this->getMock('Yamdi_OutputStream', array(), array(''), '', false, false);
		$outputMock->expects($this->once())
			->method('write')
			->with($this->equalTo($bytes))
			->will($this->returnValue(4));
		
		/* reading tag */
		$tag = new Yamdi_FlvTagSize();
		$tag->read($inputStub);
		
		$this->assertEquals(1, $tag->size);
		
		/* writing tag */
		$tag->write($outputMock);
	}
}