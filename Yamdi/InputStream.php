<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
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
	
	/**
	 * Seeks to specified position
	 *
	 * @param int $offset
	 * @return int
	 */
	public function seek($offset)
	{
		$this->cursor += $offset;
		
		return fseek($this->file_handler, $offset, SEEK_CUR);
	}
	
	/**
	 * Reads number of bytes from an input file
	 *
	 * @param int $size
	 * @return string
	 */
	public function read($size)
	{
		$this->cursor += $size;
		
		return fread($this->file_handler, $size);
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
		return fclose($this->file_handler);
	}
}