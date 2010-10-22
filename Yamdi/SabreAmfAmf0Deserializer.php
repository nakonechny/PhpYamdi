<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

/**
 * Class overrides some methods of SabreAMF_AMF0_Deserializer in order to provide ability
 * to read unserialized mixed array as an ARRAY instead of an OBJECT
 */
class Yamdi_SabreAmfAmf0Deserializer extends SabreAMF_AMF0_Deserializer
{
	/**
	 * readObject 
	 * 
	 * @return object 
	 */
	public function readObject($return_as_array = false)
	{
		$object = array();
		$this->refList[] =& $object;
		
		while (true) {
			$key = $this->readString();
			$vartype = $this->stream->readByte();
			if ($vartype == SabreAMF_AMF0_Const::DT_OBJECTTERM) {
				break;
			}
			$object[$key] = $this->readAmfData($vartype);
		}
		
		if (!defined('SABREAMF_OBJECT_AS_ARRAY') && ! $return_as_array) {
			$object = (object)$object;
		}

		return $object;
	}
	
	/**
	 * readMixedArray
	 * 
	 * @return array 
	 */
	public function readMixedArray()
	{
		$highestIndex = $this->stream->readLong();

		return $this->readObject(true);
	}
}