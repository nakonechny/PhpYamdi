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

class FlvFileHeaderTestCase extends UnitTestCase
{
	public function testReadWrite()
	{
		/* byte image of a valid flv header */ 
		$bytes = 'FLV'.
			chr(1).
			chr(5).
			chr(0).chr(0).chr(0).chr(9);

		/* preparing mock streams */
		$inputStream = new MockYamdi_InputStream();
		$inputStream->setReturnValue('read', $bytes);
		
		$outputStream = new MockYamdi_OutputStream();
		$outputStream->setReturnValue('write', strlen($bytes));
		$outputStream->expect('write', array($bytes));
		
		/* reading tag */
		$tag = new Yamdi_FlvFileHeader();
		$tag->read($inputStream);
		
		$this->assertEqual($tag->signature, 'FLV');
		$this->assertEqual($tag->version, 1);
		$this->assertEqual($tag->flags, 5);
		$this->assertEqual($tag->headersize, 9);
		
		/* writing tag */
		$tag->write($outputStream);
	}
}
