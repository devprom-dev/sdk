<?php

include_once "CssSpritesGenerator.php";

class UserPicSpritesGenerator extends CssSpritesGenerator
{
	private $sprites_mini_size = 21;
	
	private $sprites_middle_size = 30;
	
	private $sprites_usual_size = 45;

	public function storeSprites()
	{
		$files = array();
		
		$user_it = getFactory()->getObject('cms_User')->getRegistry()->Query(
				array( new SortOrderedClause() )
		);
		
		while( !$user_it->end() )
		{
			if( $user_it->getFileName('Photo') != '' )
			{
				$files[$user_it->getId()] = $user_it->getFilePath('Photo');
			}
			
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
}