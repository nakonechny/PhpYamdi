<?php
/**
 * Phpunit test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

include_once dirname(__FILE__).'/setup.php';

class InputStreamTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	protected $inputFileName;
	
	public function setUp()
	{
		// create test file
		$this->inputFileName = tempnam(sys_get_temp_dir(), '');
		$fh = fopen($this->inputFileName, 'w');
		fwrite($fh, '123abc');
		fclose($fh);
	}
	
	public function tearDown()
	{
		unlink($this->inputFileName);
	}
	
	public function testRead()
	{
		$stream = new Yamdi_InputStream($this->inputFileName);
		
		$this->assertEquals($stream->getPosition(), 0);
		$this->assertFalse($stream->isEnd());
		
		$bytes = $stream->read(3);
		$this->assertEquals($bytes, '123');
		
		$this->assertEquals($stream->getPosition(), 3);
		$this->assertEquals($bytes, '123');
		$this->assertFalse($stream->isEnd());
		
		$stream->seek(+2);
		
		$this->assertEquals($stream->getPosition(), 5);
		$this->assertFalse($stream->isEnd());
		
		$bytes = $stream->read(1);
		
		$this->assertEquals($bytes, 'c');
		$this->assertEquals($stream->getPosition(), 6);
		$this->assertFalse($stream->isEnd());
		
		/* trying to read beyont the eof */
		$bytes = $stream->read(2);
		
		$this->assertTrue($stream->isEnd()); // end of file is reached
		$this->assertEquals($stream->getPosition(), 6); // position is not changed
		$this->assertEquals($bytes, ''); // nothing has been read
		
		$stream->close();
	}
}