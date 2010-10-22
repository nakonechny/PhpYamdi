<?php
/**
 * Simpletest test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

require_once dirname(__FILE__).'/../autoload.php';

Mock::generate('Yamdi_InputStream');
Mock::generate('Yamdi_OutputStream');

class FlvTagSizeTestCase extends UnitTestCase
{
	public function testReadWrite()
	{
		$bytes = chr(0).chr(0).chr(0).chr(1); // 32-bit big-endian integer 
		
		/* preparing mock streams */
		$inputStream = new MockYamdi_InputStream();
		$inputStream->setReturnValue('read', $bytes);
		
		$outputStream = new MockYamdi_OutputStream();
		$outputStream->setReturnValue('write', 4);
		$outputStream->expectOnce('write', array($bytes));
		
		/* reading tag */
		$tag = new Yamdi_FlvTagSize();
		$tag->read($inputStream);
		
		$this->assertEqual($tag->size, 1);
		
		/* writing tag */
		$tag->write($outputStream);
	}
}
