<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_FlvFileHeader extends Yamdi_Struct
{
	const FLAG_AUDIO = 4;
	const FLAG_VIDEO = 1;
	
	protected $dataTypes = array(
		'signature' => 'a3', 	// Always “FLV”
		'version' => 'c', 		// Currently 1 for known FLV files
		'flags' => 'c',			// Bitmask: 4 is audio, 1 is video
		'headersize' => 'N'		// Total size of header (always 9 for known FLV files)
	);
	
	public function isValid() {
		return $this->signature == 'FLV'
			&& $this->version = 1
			&& ($this->hasVideo() || $this->hasAudio()) 
			&& $this->headersize == 9;
	}
	
	public function hasVideo(){
		return $this->flags & self::FLAG_VIDEO;
	}

	public function hasAudio(){
		return $this->flags & self::FLAG_AUDIO;
	}
}
