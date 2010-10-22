<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_AmfDeserializer
{
	/**
	 * @var SabreAMF_AMF0_Deserializer
	 */
	protected $deserializer;
	
	public function __construct($string)
	{
		$this->deserializer = new Yamdi_SabreAmfAmf0Deserializer(new SabreAMF_InputStream($string));
	}
	
	public function read($type = null)
	{
		return $this->deserializer->readAMFData($type);
	}
}