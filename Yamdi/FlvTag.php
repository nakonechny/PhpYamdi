<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

/**
 * Class of a FlvTag block of FLV file
 * 
 * @see http://osflash.org/flv#flv_format
 */
class Yamdi_FlvTag extends Yamdi_Struct
{
	const TYPE_AUDIO = 0x08;  	// Tag contains an audio packet similar to a SWF SoundStreamBlock plus codec information
	const TYPE_VIDEO = 0x09;  	// Tag contains a video packet similar to a SWF VideoFrame plus codec information
	const TYPE_META = 0x12;  	// Tag contains two AMF packets, the name of the event and the data to go with it
	
	protected $dataTypes = array(
		'type' => 'c', 			// Determines the layout of Body, see below for tag types 
		'datasize' => 'C3', 	// Size of Body (total tag size - 11)  
		'timestamp' => 'C3',		// Timestamp of tag (in milliseconds)
		'timestamp_ex' => 'C1',		// Timestamp of tag (in milliseconds)
		'streamid' => 'C3'		// Always 0
		// followed by tag Body
	);
	
	protected $bodyBytesRead = 0;
	
	/**
	 * Imports a tag block from an input stream
	 * 
	 * @param Yamdi_InputStream $stream
	 * @return bool
	 */
	public function read(Yamdi_InputStream $stream)
	{
		$this->bodyBytesRead = 0;
		return parent::read($stream);
	}
	
	/**
	 * Checks wether current FlvTag contains audio packet
	 * 
	 * @return bool
	 */
	public function isAudio() {
		return $this->type == self::TYPE_AUDIO;
	}

	/**
	 * Checks wether current FlvTag contains video packet
	 * 
	 * @return bool
	 */
	public function isVideo() {
		return $this->type == self::TYPE_VIDEO;
	}
	
	/**
	 * Checks wether current FlvTag contains AMF packets
	 * 
	 * @return bool
	 */
	public function isMeta() {
		return $this->type == self::TYPE_META;
	}
	
	/**
	 * Checks wether video FlvTag body contains a keyframe
	 * 
	 * First byte of current tag body must be read to determine a frame type
	 * 
	 * @param Yamdi_InputStream $stream
	 * @return bool
	 */
	public function checkIfKeyFrame(Yamdi_InputStream $stream)
	{
		$first_byte = ord($this->readTagBodyBytes($stream, 1));
		return (($first_byte & 0xf0) >> 4) == 1; //frameType == keyframe
	}

	/**
	 * Checks wether current tag is valid FlvTag
	 * 
	 * @return bool
	 */
	public function isValid() {
		return ($this->isMeta() || $this->isAudio() || $this->isVideo())
			&& $this->streamid1 == 0
			&& $this->streamid2 == 0
			&& $this->streamid3 == 0;
	}
	
	/**
	 * Returns size of tag data
	 * 
	 * @return int  
	 */
	public function getDataSize() {
		return $this->datasize3 + ($this->datasize2 *256) + ($this->datasize1 *256*256);
	}

	/**
	 * Sets size of tag data
	 * 
	 * @param int $value
	 */
	public function setDataSize($value) {
		$this->datasize3 = $value & 255;
		$this->datasize2 = ($value/256) & 255;
		$this->datasize1 = ($value/256/256) & 255;
	}
	
	/**
	 * Returns timestamp of a tag in milliseconds
	 *
	 * @return int
	 */
	public function getTimestamp()
	{
		return ($this->timestamp_ex *256*256*256) 
			+ ($this->timestamp1 << 16) 
			+ ($this->timestamp2 << 8) 
			+ $this->timestamp3; 	
	}
	
	public function skipTagBody(Yamdi_InputStream $stream) {
		$read = $this->bodyBytesRead;
		$this->bodyBytesRead = 0;
		$stream->seek($this->getDataSize() - $read);
	}

	public function readTagBody(Yamdi_InputStream $stream) {
		$read = $this->bodyBytesRead;
		$this->bodyBytesRead = 0;
		return $stream->read($this->getDataSize() - $read);
	}

	public function readTagBodyBytes(Yamdi_InputStream $stream, $count) {
		$this->bodyBytesRead += $count;
		return $stream->read($count);
	}
}
