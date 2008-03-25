<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

class Yamdi_FlvMetadataBody
{
	protected $mixedArray = array();
	protected $string;

	public function read($string)
	{
		/*
		 * The contents of a meta packet are two AMF packets.
		 * The first is almost always a short uint16_be length-prefixed UTF-8 string (AMF type 0×02),
		 * and the second is typically a mixed array (AMF type 0×08).
		 * However, the second chunk typically contains a variety of types,
		 * so a full AMF parser should be used.
		 */
		$deserializer = new  Yamdi_AmfDeserializer($string);
		
		$this->string = $deserializer->read();
		$this->mixedArray = $deserializer->read();
	}
	
	public function write()
	{
		$serializer = new Yamdi_AmfSerializer();
		
		$serializer->write($this->string, SabreAMF_AMF0_Const::DT_STRING);
		$serializer->write($this->mixedArray, SabreAMF_AMF0_Const::DT_MIXEDARRAY);
		
		return $serializer->getOutput();
	}
	
	public function getSize()
	{
		return strlen($this->write());
	}
	
	public function __get($name)
	{
		if (array_key_exists($name, $this->mixedArray)) {
			return $this->mixedArray[$name];
		} else {
			return null;
		}
	}

	public function __set($name, $value)
	{
		return $this->mixedArray[$name] = $value;
	}
}
