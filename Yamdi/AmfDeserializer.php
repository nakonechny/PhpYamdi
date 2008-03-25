<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

class Yamdi_AmfDeserializer
{
	/**
	 * @var SabreAMF_AMF0_Deserializer
	 */
	protected $deserializer;
	
	public function __construct($string)
	{
		$this->deserializer = new Yamdi_SabreAmfAmf0DeserializerFixed(new SabreAMF_InputStream($string));
	}
	
	public function read($type = null)
	{
		return $this->deserializer->readAMFData($type);
	}
}