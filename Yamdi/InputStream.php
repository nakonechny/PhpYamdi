<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_InputStream
{
	/**
	 * handler of an intput file
	 *
	 * @var resource
	 */
	protected $file_handler;
	
	/**
	 * current read position of an input file
	 *
	 * @var int
	 */
	protected $cursor = 0;
	
	/**
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->file_handler = fopen($filename, 'rb');
		$this->cursor = 0;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	/**
	 * Seeks on input stream
	 * with specified offset from current position ($whence = SEEK_CUR)
	 * or with absolute position ($whence = SEEK_SET)
	 *
	 * @param int $offset
	 * @param int $whence
	 * @return int
	 */
	public function seek($offset, $whence = SEEK_CUR)
	{
		switch ($whence)
		{
			case SEEK_CUR:
				$this->cursor += $offset;
				break;
				
			case SEEK_SET:
				$this->cursor = $offset;
				break;
			
			default:
				throw new Exception('Unsupported seek mode'); 
				break;
		}
		
		return fseek($this->file_handler, $offset, $whence);
	}
	
	/**
	 * Reads number of bytes from an input file
	 *
	 * @param int $size
	 * @return string
	 */
	public function read($size)
	{
		$bytes = fread($this->file_handler, $size);
		$this->cursor += strlen($bytes);
		
		return $bytes;
	}
	
	/**
	 * Returns current nput file position
	 * 
	 * @return int 
	 */
	public function getPosition() {
		return $this->cursor;
	}
	
	/**
	 * Checks wether an end of an input file is reached
	 * 
	 * @return bool
	 */
	public function isEnd() {
		return feof($this->file_handler);
	}
	
	/**
	 * Closes an input file
	 * 
	 * @return bool
	 */
	public function close()
	{
		if (is_resource($this->file_handler)) {
			return fclose($this->file_handler);
		} else {
			return false;
		}
	}
}