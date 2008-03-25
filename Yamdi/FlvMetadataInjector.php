<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

class Yamdi_FlvMetadataInjector extends Yamdi_FlvFile
{
	/**
	 * @var array(int)
	 */
	protected $filepositions;
	/**
	 * @var array(float)
	 */
	protected $times;
	
	public function run($source, $destination)
	{
		$this->read($source);
		$this->assignMetadata();
		$this->write($destination);
	}
	
	protected function assignMetadata()
	{
		/*
		 * Now it's time to assign any desired metadata fields
		 */
		$this->metadata->metadatacreator = "Allen's Php_Yamdi version ".self::$version;
	}
	
	/**
	 * Decomposes a file into flv tags while reading. Searches for keyframes
	 * 
	 * @param string $filename
	 * @return Yamdi_InputStream
	 */
	protected function read($filename)
	{
		$stream = parent::read($filename);

		$this->filepositions	= array();
		$this->times			= array();
		
		$tag = new Yamdi_FlvTag();
		
		while (!$stream->isEnd())
		{
			$tag_position = $stream->getPosition();
			
			/*
			 * Tag
			 */
			if (!$tag->read($stream)) {
				break;
			}
			if (!$tag->isValid()) {
				throw new Exception('Invalid tag found');
			}
			
			if ($tag->isVideo()) {
				if ($tag->checkIfKeyFrame($stream))
				{
					$this->filepositions[] = $tag_position;
					$this->times[] = $tag->getTimestamp() / 1000.0;
				}
			}
			
			$tag->skipTagBody($stream);	// wind forward to next tag
			
			/*
			 * Previous tag size
			 */
			$tagSize = new Yamdi_FlvTagSize();
			if (!$tagSize->read($stream)) {
				break;
			}
		}
		
		return null; //nothing else to read
	}
	
	protected function shiftMetaSize()
	{
		/*
		 * Injecting calculated keyframes
		 */
		$keyframes = new stdClass();
		$keyframes->filepositions = $this->filepositions;
		$keyframes->times = $this->times;
		$this->metadata->keyframes = $keyframes;

		$shift = parent::shiftMetaSize();
		
		/*
		 * Shifting filepositions
		 */
		foreach ($this->filepositions as $key=>$value) {
			$this->filepositions[$key] = $value + $shift;
		}

		/*
		 * Updating keyframes with shifted filepositions
		 */
		$keyframes->filepositions = $this->filepositions;
		$this->metadata->keyframes = $keyframes;

		return $shift;
	}
}