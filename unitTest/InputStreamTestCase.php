<?php
/**
 * Simpletest test case
 * 
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

require_once dirname(__FILE__).'/../autoload.php';

class InputStreamTestCase extends UnitTestCase
{
	/**
	 * @var string
	 */
	protected $testFileName = '/tmp/test_file.flv';
	
	public function setUp()
	{
		/* creating test file */
		$fh = fopen($this->testFileName, 'w');
		fwrite($fh, '123abc');
		fclose($fh);
	}
	
	public function tearDown()
	{
		unlink($this->testFileName);
	}
	
	public function testRead()
	{
		$stream = new Yamdi_InputStream($this->testFileName);
		
		$this->assertEqual($stream->getPosition(), 0);
		$this->assertFalse($stream->isEnd());
		
		$bytes = $stream->read(3);		$this->assertEqual($bytes, '123');
		
		$this->assertEqual($stream->getPosition(), 3);
		$this->assertEqual($bytes, '123');
		$this->assertFalse($stream->isEnd());
		
		$stream->seek(+2);
		
		$this->assertEqual($stream->getPosition(), 5);
		$this->assertFalse($stream->isEnd());
		
		$bytes = $stream->read(1);
		
		$this->assertEqual($bytes, 'c');
		$this->assertEqual($stream->getPosition(), 6);
		$this->assertFalse($stream->isEnd());
		
		/* trying to read beyont the eof */
		$bytes = $stream->read(2);
		
		$this->assertTrue($stream->isEnd()); // end of file is reached
		$this->assertEqual($stream->getPosition(), 6); // position is not changed
		$this->assertEqual($bytes, ''); // void is read
		
		$stream->close();
	}
}
