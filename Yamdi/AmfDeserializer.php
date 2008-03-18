<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

require_once 'SabreAMF/AMF0/Deserializer.php';
require_once dirname(__FILE__).'/SabreAmfAmf0DeserializerFixed.php';

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