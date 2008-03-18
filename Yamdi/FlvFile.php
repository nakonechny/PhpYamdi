<?php
/**
 * @author Allen
 * @package PhpYamdi (http://phpyamdi.berlios.de/)
 */

require_once dirname(__FILE__).'/Struct.php';
require_once dirname(__FILE__).'/FlvFileHeader.php';
require_once dirname(__FILE__).'/FlvTag.php';
require_once dirname(__FILE__).'/FlvTagSize.php';
require_once dirname(__FILE__).'/FlvMetadataBody.php';
require_once dirname(__FILE__).'/InputStream.php';
require_once dirname(__FILE__).'/OutputStream.php';

class Yamdi_FlvFile
{
	static $version = '0.1';
	
	protected $sourceFilename;
	
	/*
	 * File parts 
	 */
	
	/**
	 * @var Yamdi_FlvFileHeader
	 */
	protected $header;

	/**
	 * @var Yamdi_FlvTagSize
	 */
	protected $zeroTagSize;

	/**
	 * @var Yamdi_FlvMetadataBody
	 */
	protected $metadata;

	/**
	 * @var Yamdi_FlvTag
	 */
	protected $metadataTag;
	
	/**
	 * @var Yamdi_FlvTagSize
	 */
	protected $metadataTagSize;

	/*
	 * Calculated values
	 */
	
	/**
	 * @var array(int)
	 */
	protected $filepositions;
	/**
	 * @var array(float)
	 */
	protected $times;
	
	/**
	 * @var int
	 */
	protected $metadataTagBodyLenghth;
	
	/**
	 * @var int
	 */
	protected $mediaTagsStartPosition;

	public function __construct()
	{
		$this->header		= new Yamdi_FlvFileHeader();
		$this->zeroTagSize	= new Yamdi_FlvTagSize();
		$this->metadata		= new Yamdi_FlvMetadataBody();
		$this->metadataTag	= new Yamdi_FlvTag();
		$this->metadataTagSize = new Yamdi_FlvTagSize();
	}
	
	public function run($source, $destination)
	{
		$this->read($source);
		$this->metadata->metadatacreator = "Allen's Php_Yamdi version ".self::$version; 
		$this->injectKeyframes();
		$this->write($destination);
	}
	
	/**
	 * Decomposes a file into flv tags while reading. Searches for keyframes
	 * 
	 * @param string $filename
	 */
	protected function read($filename)
	{
		$this->sourceFilename = $filename;
		
		/*
		 * File to read 
		 */
		$stream = new Yamdi_InputStream($this->sourceFilename);

		/*
		 * Constant header
		 */
		$this->header->read($stream);
		if (!$this->header->isValid()) {
			throw new Exception('File '.$this->sourceFilename.' is not of flv format');
		}
		
		/*
		 * Constant size of zero tag
		 */
		$this->zeroTagSize->read($stream);
		// $this->zeroTagSize->size should be zero
		
		/*
		 * First tag (meta)
		 */
		$this->metadataTag->read($stream);
		if (!$this->metadataTag->isMeta()) {
			throw new Exception('No metadata tag found if file '.$this->sourceFilename);
		}
		$tagBodyString = $this->metadataTag->readTagBody($stream);
		$this->metadataTagBodyLenghth = strlen($tagBodyString);
		$this->metadata->read($tagBodyString);
		
		/*
		 * Previous tag size
		 */
		$this->metadataTagSize->read($stream);

		$this->filepositions	= array();
		$this->times			= array();
		
		$this->mediaTagsStartPosition = $stream->getPosition();
		
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

		/*
		 * Cleanup
		 */
		$stream->close();
	}
	
	protected function injectKeyframes()
	{
		/*
		 * Calculating byte shift
		 */
		$old_size = $this->metadataTagBodyLenghth;
		
		$keyframes = new stdClass();
		$keyframes->filepositions = $this->filepositions;
		$keyframes->times = $this->times;
		$this->metadata->keyframes = $keyframes;
		
		$new_size = $this->metadata->getSize();
		
		$fileposition_shift = $new_size - $old_size;
		
		/*
		 * Shifting filepositions
		 */
		foreach ($this->filepositions as $key=>$value) {
			$this->filepositions[$key] = $value + $fileposition_shift;
		}

		/*
		 * Assigning keyfremes with shifted filepositions
		 */
		$keyframes->filepositions = $this->filepositions;
		$this->metadata->keyframes = $keyframes;
		
		$this->metadataTagSize->size += $fileposition_shift;
		
		$this->metadataTag->setDataSize($new_size);
	}
	
	protected function write($filename)
	{
		/*
		 * File to write
		 */
		$stream = new Yamdi_OutputStream($filename);
		
		if (! $this->header->isValid()) {
			throw new Exception('Must read an flv file before writing it');
		}
		
		/*
		 * Writing preamble tags
		 */
		
		$this->header->write($stream);
		$this->zeroTagSize->write($stream);
		$this->metadataTag->write($stream);
		$stream->write($this->metadata->write());
		$this->metadataTagSize->write($stream);
		
		/*
		 * Writing media tags (passing through with no changes)
		 */
		
		$stream->passthrough(new Yamdi_InputStream($this->sourceFilename), $this->mediaTagsStartPosition);
		
		$stream->close();
	}
}
