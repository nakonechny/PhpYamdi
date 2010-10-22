<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_AmfSerializer
{
	/**
	 * @var SabreAMF_OutputStream
	 */
	protected $outputStream;
	
	/**
	 * @var SabreAMF_AMF0_Serializer
	 */
	protected $serializer;
	
	public function __construct()
	{
		$this->outputStream = new SabreAMF_OutputStream();
		$this->serializer = new SabreAMF_AMF0_Serializer($this->outputStream);
	}
	
	public function write($data, $type = null)
	{
		return $this->serializer->writeAMFData($data, $type);
	}
	
	public function getOutput()
	{
		return $this->outputStream->getRawData();
	}
}