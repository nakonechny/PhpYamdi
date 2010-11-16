<?php
/**
 * Phpunit test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

include_once dirname(__FILE__).'/setup.php';

class OutputStreamTest extends PHPUnit_Framework_TestCase
{
	protected $inputFileName;
	protected $testFileName;
	
	public function setUp()
	{
		// write test file for passthrough
		$this->inputFileName = tempnam(sys_get_temp_dir(), '');
		$fh = fopen($this->inputFileName, 'w');
		fwrite($fh, '123abc');
		fclose($fh);
		
		$this->outtputFileName = tempnam(sys_get_temp_dir(), '');
	}
	
	public function tearDown()
	{
		unlink($this->inputFileName);
		unlink($this->outtputFileName);
	}
	
	public function testWriteAndPassthroughFromPosition()
	{
		$stream = new Yamdi_OutputStream($this->outtputFileName);

		// writing bytes
		$stream->write('321');
		
		// passing input source through
		$inputStream = new Yamdi_InputStream($this->inputFileName);
		$stream->passthrough($inputStream, 3);
		$inputStream->close();
		
		// reading written file back
		$fh = fopen($this->outtputFileName, 'r');
		$bytes = fread($fh, 6+1); // reading until the eof
		
		// assert file length is 6 bytes
		$this->assertTrue(feof($fh));
		
		// assert writen 3 bytes from $stream->write() and 3 bytes from $stream->passthrough()
		$this->assertEquals($bytes, '321abc');
		
		fclose($fh);
		$stream->close();
	}
}