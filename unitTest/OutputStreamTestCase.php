<?php
/**
 * Simpletest test case
 * 
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

require_once dirname(__FILE__).'/../autoload.php';

class OutputStreamTestCase extends UnitTestCase
{
	/**
	 * @var string
	 */
	protected $passthroughFileName = '/tmp/test_file_pass.flv';
	
	/**
	 * @var string
	 */
	protected $testFileName = '/tmp/test_file.flv';
	
	public function setUp()
	{
		/* creating test file for passthrough */
		$fh = fopen($this->passthroughFileName, 'w');
		fwrite($fh, '123abc');
		fclose($fh);
	}
	
	public function tearDown()
	{
		unlink($this->passthroughFileName);
		unlink($this->testFileName);
	}
	
	public function testWrite()
	{
		$stream = new Yamdi_OutputStream($this->testFileName);

		/* writing bytes */
		$stream->write('321');
		
		/* passing input source through */
		$inputStream = new Yamdi_InputStream($this->passthroughFileName);
		$stream->passthrough($inputStream, 3);
		$inputStream->close();
		
		/* checking writen data */
		
		$fh = fopen($this->testFileName, 'r');
		$bytes = fread($fh, 6+1); // reading beyond the eof
		
		$this->assertTrue(feof($fh));
		$this->assertEqual($bytes, '321abc');
		
		fclose($fh);
		$stream->close();
	}
}
