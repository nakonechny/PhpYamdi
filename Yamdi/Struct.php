<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

/**
 * Class emulates C structures stored as strings
 */
abstract class Yamdi_Struct
{
	/**
	 * Structure data
	 *
	 * @var array(mixed)
	 */
	protected $data;
	
	/**
	 * Types of structure data in pack/unpack notation
	 *
	 * @var array(string)
	 */
	protected $dataTypes;
	
	/**
	 * Size in bytes of all structure types
	 *
	 * @var array(int)
	 */
	static protected $typeSize = array(
			'a' => 1, // NUL-padded string
			'A' => 1, // SPACE-padded string
			'h' => 1, // Hex string, low nibble first
			'H' => 1, // Hex string, high nibble first
			'c' => 1, // signed char
			'C' => 1, // unsigned char
			's' => 2, // signed short (always 16 bit, machine byte order)
			'S' => 2, // unsigned short (always 16 bit, machine byte order)
			'n' => 2, // unsigned short (always 16 bit, big endian byte order)
			'v' => 2, // unsigned short (always 16 bit, little endian byte order)
			'i' => 4, // signed integer (machine dependent size and byte order)
			'I' => 4, // unsigned integer (machine dependent size and byte order)
			'l' => 4, // signed long (always 32 bit, machine byte order)
			'L' => 4, // unsigned long (always 32 bit, machine byte order)
			'N' => 4, // unsigned long (always 32 bit, big endian byte order)
			'V' => 4, // unsigned long (always 32 bit, little endian byte order)
			'f' => 4, // float (machine dependent size and representation)
			'd' => 8, // double (machine dependent size and representation)
			'x' => 0, // NUL byte
			'X' => -1, // Back up one byte
			'@' => 0 // NUL-fill to absolute position
	);
	
	public function __construct()
	{
		foreach ($this->dataTypes as $key=>$type) {
			$this->data[$key] = null;
		}
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		} else {
			return null;
		}
	}

	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name] = $value;
		} else {
			return null;
		}
	}
	
	public function unpack($input)
	{
		$this->data = unpack($this->getUnpackFormat(), $input);
		
		return $this->data !== false;
	}

	public function pack()
	{
		$quoted_data = array();
		foreach($this->data as $key=>$value) {
			$quoted_data[$key] = in_array($this->dataTypes[$key], array('a', 'A', 'h', 'H')) ? '"'.(string)$value.'"' : $value;
		}

		$pach_cmd = 'return pack("'.$this->getPackFormat().'", "'.implode('", "', $quoted_data).'");';
		
		return eval($pach_cmd);
	}

	protected function getUnpackFormat()
	{
		$pairs = array();
		foreach ($this->dataTypes as $name => $type)
		{
			$pairs[] = $type . $name;
		}
		
		return implode('/', $pairs);
	}
	
	protected function getPackFormat()
	{
		return $this->compressPackFormatString(implode('',$this->dataTypes));
	}
	
	/**
	 * Replaces repeating chars by letter and a number of chars
	 * @deprecated?
	 */
	protected function compressPackFormatString($string)
	{
		$result = '';
		$prev_char = '';
		$c = 1;
		for ($i=0; $i<=(strlen($string)+1); $i++) {
			$char = substr($string, $i, 1);
			if ($char !== $prev_char)
			{
				if ($c > 1) {
					$result .= $c;
					$c = 1;
				}
				$result .= $char;
				$prev_char = $char;
			}
			else {
				$c++;
			}
		}
		
		return $result;
	}

	public function calculateSize()
	{
		$count = 0;
		
		foreach ($this->dataTypes as $type) {
			if (strlen($type) == 1) {
				$count += self::$typeSize[$type];
			} else {
				$count += self::$typeSize[substr($type, 0, 1)] * substr($type, 1);
			}
		}
		
		return $count;
	}
	
	public function read(Yamdi_InputStream $stream)
	{
		if ($stream->isEnd()) {
			return false;
		}
		
		$content = $stream->read($this->calculateSize());
		
		if ($content === false || $content == '') {
			return false;
		} else {
			return $this->unpack($content);
		}
	}
	
	public function write(Yamdi_OutputStream $stream)
	{
		return $stream->write($this->pack());
	}
}