<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

/**
 * Some methods of SabreAMF_AMF0_Deserializer is reloaded to provide ability 
 * to read mixed array as an ARRARY instead of an object
 */
class Yamdi_SabreAmfAmf0DeserializerFixed extends SabreAMF_AMF0_Deserializer
{
	/**
	 * readObject 
	 * 
	 * @return object 
	 */
	public function readObject($as_array = false)
	{
		$object = array();
		
		while (true) {
			$key = $this->readString();
			$vartype = $this->stream->readByte();
			if ($vartype == SabreAMF_AMF0_Const::DT_OBJECTTERM) {
				break;
			}
			$object[$key] = $this->readAmfData($vartype);
		}
		
		if (!defined('SABREAMF_OBJECT_AS_ARRAY') && ! $as_array)
		{
			$object = (object)$object;
		}
		
		$this->refList[] = $object;

		return $object;
	}
	
	/**
	 * readMixedArray
	 * 
	 * @return array 
	 */
	public function readMixedArray()
	{
		$this->stream->readLong();
		
		return $this->readObject(true);
	}
}