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
		/*
		 * Drop all metadata instead of video parameters
		 */
		/** @todo */
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
						$this->videoTagPosition = $tag_position; // initialize with first tag position
						$this->videoBlockSize = $tag->getDataSize() + 4 + 11;
					}
					
					if (++$keyframe_number == $this->desiredKeyframeNumber )
					{
						$this->videoTagPosition = $tag_position; //found keyframe
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