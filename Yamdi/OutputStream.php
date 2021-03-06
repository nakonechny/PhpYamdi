<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_OutputStream
{
	/**
	 * handler of an output file
	 *
	 * @var resource
	 */
	protected $file_handler;
	
	/**
	 * Size of a portion of bytes used in passthrough
	 *
	 * @var int
	 */
	protected $passthrough_chunk_size = 524288;
	
	/**
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->file_handler = fopen($filename, 'w');
	}
	
	/**
	 * Writes chunk of data to an output file
	 *
	 * @param string $chunk
	 * @return int
	 */
	public function write($chunk)
	{
		return fwrite($this->file_handler, $chunk);
	}
	
	
	/**
	 * Translates data from $source to an output file without changes
	 *
	 * @param Yamdi_InputStream $source
	 * @param int $fromPosition
	 */
	public function passthrough(Yamdi_InputStream $source, $fromPosition, $size = null)
	{
		$source->seek($fromPosition);
		
		if (null === $size) {
			while($data = $source->read($this->passthrough_chunk_size)) {
				$this->write($data);
			}
		} else {
			$this->write($source->read($size));
		}
	}
	
	/**
	 * Closes an output file
	 *
	 * @return bool
	 */
	public function close()
	{
		return fclose($this->file_handler);
	}
}