<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

class Yamdi_FlvFileHeader extends Yamdi_Struct
{
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
		return $this->flags & 1;
	}

	public function hasAudio(){
		return $this->flags & 4;
	}
}
