<?php
use LetterAvatar\LetterAvatar;
use LetterAvatar\ColorPalette;
include_once "CssSpritesGenerator.php";

class UserPicSpritesGenerator extends CssSpritesGenerator
{
	private $sprites_mini_size = 18;
	
	private $sprites_middle_size = 30;
	
	private $sprites_usual_size = 45;

	public function storeSprites()
	{
		$files = array();
		
		$user_it = getFactory()->getObject('cms_User')->getRegistry()->Query(
				array( new SortOrderedClause() )
		);
		while( !$user_it->end() ) {
			$files[$user_it->getId()] = $this->getPhotoFilePath($user_it);
			$user_it->moveNext();
		}

		UserPicSpritesGenerator::storeSpriteFile( 
				SERVER_ROOT_PATH."images/userpics-mini.png", 
				$files, 
				$this->sprites_mini_size, 
				$this->sprites_mini_size, 
				SERVER_ROOT_PATH."images/userpic-grey.png"
		);

		UserPicSpritesGenerator::storeSpriteFile( 
				SERVER_ROOT_PATH."images/userpics-middle.png", 
				$files, 
				$this->sprites_middle_size, 
				$this->sprites_middle_size, 
				SERVER_ROOT_PATH."images/userpic-grey.png"
		);
		
		UserPicSpritesGenerator::storeSpriteFile( 
				SERVER_ROOT_PATH."images/userpics.png", 
				$files, 
				$this->sprites_usual_size, 
				$this->sprites_usual_size, 
				SERVER_ROOT_PATH."images/userpic-grey.png"
		);
	}

	protected function getPhotoFilePath( $user_it )
	{
		if( $user_it->getFileName('Photo') != '' ) return $user_it->getFilePath('Photo');

		if ( count($this->colors) < 1 ) {
			$this->colors = ColorPalette::getColors();
		}
		$background = $user_it->getId() % count($this->colors);

		$title = trim(join('', array_map(
			function($value) {
				return mb_substr(mb_strtoupper($value),0,1);
			},
			array_slice(explode(' ', $user_it->getDisplayName()),0,2)
		)));
		$filePath = tempnam(sys_get_temp_dir(), 'sprite_');

		$letterAvatar = new LetterAvatar;
		$letterAvatar
			->setBackgroundColors(array($this->colors[$background]))
			->setFontRatio(0.6)
			->setFontFile(SERVER_ROOT_PATH."ext/mpdf50/ttfonts/DejaVuSansCondensed.ttf")
			->generate(array($title), 120)
			->saveAsPng($filePath);

		return $filePath;
	}

	private $colors = array();
}