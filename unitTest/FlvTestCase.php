<?php
require_once dirname(__FILE__).'/../autoload.php';

class FlvTestCase extends UnitTestCase
{
	/**
	 * @deprecated 
	 */
	public function testFileRead()
	{
		/*
		 * Here we need a real correct flv file
		 */
		$stream = new Yamdi_InputStream(dirname(__FILE__).'/../trailer_2.flv');

		/*
		 * Constant header
		 */
		$header = new Yamdi_FlvFileHeader();
		$header->read($stream);
		$this->assertEqual(true, $header->isValid());
		
		/*
		 * Constant size of zero tag
		 */
		$zeroTagSize = new Yamdi_FlvTagSize();
		$zeroTagSize->read($stream);
		$this->assertEqual(0, $zeroTagSize->size); // allways 0
		
		/*
		 * First tag (should be meta)
		 */
		$tag = new Yamdi_FlvTag();
		$tag->read($stream);
		$this->assertTrue($tag->isMeta());
		$metadata = new Yamdi_FlvMetadataBody();
		$metadata->read($tag->readTagBody($stream));
		
		/*
		 * Previous tag size
		 */
		$tagSize = new Yamdi_FlvTagSize();
		$tagSize->read($stream);
		
		while (!$stream->isEnd()) {
			
			$tag_position = $stream->getPosition();
			
			/*
			 * Tag
			 */
			if (!$tag->read($stream)) {
				break;
			}
			$this->assertTrue($tag->isValid());
			
			if ($tag->isVideo()) {
				if ($tag->checkIfKeyFrame($stream))
				{
					$new_filepositions[] = $tag_position;
					$new_times[] = $tag->getTimestamp() / 1000.0;
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

		$this->assertTrue(is_array($new_filepositions));
		$this->assertTrue(count($new_filepositions) > 0);
		
		$this->assertTrue(is_array($new_times));
		$this->assertTrue(count($new_times) > 0);
		
		$this->assertEqual(count($new_times), count($new_filepositions));
		
		/*
		 * Cleanup
		 */
		$stream->close();
	}

	/**
	 * @deprecated 
	 */
	public function testMetadataWriteback()
	{
		/*
		 * File to read 
		 */
		$stream = new Yamdi_InputStream(dirname(__FILE__).'/../trailer_2.flv');

		/*
		 * Constant header
		 */
		$header = new Yamdi_FlvFileHeader();
		$header->read($stream);
		$this->assertEqual(true, $header->isValid());
		
		/*
		 * Constant size of zero tag
		 */
		$zeroTagSize = new Yamdi_FlvTagSize();
		$zeroTagSize->read($stream);
		$this->assertEqual(0, $zeroTagSize->size); // allways 0
		
		/*
		 * First tag (should be meta)
		 */
		$tag = new Yamdi_FlvTag();
		$tag->read($stream);
		$this->assertTrue($tag->isMeta());
		$metadata = new Yamdi_FlvMetadataBody();
		$tagBody = $tag->readTagBody($stream);
		$metadata->read($tagBody);

		$this->assertEqual(strlen($tagBody), strlen($metadata->write()));
	
		/*
		 * Cleanup
		 */
		$stream->close();
	}
}
