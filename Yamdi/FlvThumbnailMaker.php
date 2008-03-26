<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

class Yamdi_FlvThumbnailMaker extends Yamdi_FlvFile
{
	protected $videoTagPosition;
	protected $videoBlockSize;
	protected $desiredKeyframeNumber;
	
	public function run($source, $destination, $keyframe_num = 1)
	{
		$this->desiredKeyframeNumber = $keyframe_num;
		
		$this->read($source);
		$this->assignMetadata();
		$this->write($destination);
	}
	
	protected function assignMetadata()
	{
		$this->header->flags = Yamdi_FlvFileHeader::FLAG_VIDEO;
		
		/*
		 * Drop all metadata (except of video parameters?)
		 */
		$this->metadata->clear();
	}
	
	/**
	 * Decomposes a file into flv tags while reading. Searches for given keyframe
	 * 
	 * @param string $filename
	 * @return Yamdi_InputStream
	 */
	protected function read($filename)
	{
		$stream = parent::read($filename);

		/*
		 * Looking for needed keyframe video tag
		 */
		if ($this->metadata->keyframes !== null)
		{
			/*
			 * Keyframes present in meta, getting position of needed tag
			 */
			$filepositions = $this->metadata->keyframes->filepositions; 
			if (isset($filepositions[$this->desiredKeyframeNumber])) {
				$this->videoTagPosition = $filepositions[$this->desiredKeyframeNumber];
			} else {
				$this->videoTagPosition = $filepositions[0];
			}
			
			/*
			 * Reading tag to fetch block size
			 */
			$stream->seek($this->videoTagPosition, SEEK_SET);

			$tag = new Yamdi_FlvTag();
			if (!$tag->read($stream)) {
				throw new Exception('Invalid tag fileposition');
			}
			if (!$tag->isValid()) {
				throw new Exception('Invalid tag found');
			}
			if (!$tag->isVideo()) {
				throw new Exception('Specified tag is not video');
			}
			if (!$tag->checkIfKeyFrame($stream)) {
				throw new Exception('Specified tag is not a keyframe');
			}
			$this->videoBlockSize = $tag->getDataSize() + 4 + 11;
		}
		else
		{
			/*
			 * Keyframes absent in meta, reading tags until needed keyframe is found
			 */
			$tag = new Yamdi_FlvTag();
			$keyframe_number = 0;
			
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
						if ($this->videoTagPosition === null) {
							// initializing with first tag position and size
							$this->videoTagPosition = $tag_position; 
							$this->videoBlockSize = $tag->getDataSize() + 4 + 11;
						}
						
						if (++$keyframe_number == $this->desiredKeyframeNumber )
						{
							$this->videoTagPosition = $tag_position; //found keyframe
							$this->videoBlockSize = $tag->getDataSize() + 4 + 11;
							break;
						}
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
		}
		
		return null; //nothing else to read 
	}
	
	/**
	 * Writes only one video tag
	 * 
	 * @param Yamdi_OutputStream $stream
	 */
	protected function writeMediaBlocks($stream)
	{
		$stream->passthrough(
			new Yamdi_InputStream($this->sourceFilename),
			$this->videoTagPosition,
			$this->videoBlockSize
		);
		
/*		$video_tag = new Yamdi_FlvTag();
		$video_tag_body = '';
		$size_tag = new Yamdi_FlvTagSize();
		
		*//** read *//*
		$input_stream = new Yamdi_InputStream($this->sourceFilename);
		$input_stream->seek($this->videoTagPosition);
		$video_tag->read($input_stream);
		$video_tag_body = $input_stream->read($video_tag->getDataSize());
		$size_tag->read($input_stream);		
		
		*//** write *//*
		$video_tag->write($stream);
		$stream->write($video_tag_body);
		$size_tag->write($stream);
*/	}
}