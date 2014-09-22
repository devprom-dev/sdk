<?php

include "LanguageEntityIterator.php";
include "persisters/LanguageSettingsPersister.php";

class LanguageEntity extends Metaobject
{
	function LanguageEntity()
	{
		parent::Metaobject('cms_Language');
		
 		$this->addAttribute( 
 			'DateFormatClass', 'VARCHAR', 
				text(1162), true, false, '' );
				
		$this->addPersister( new LanguageSettingsPersister() );
	}

	function createIterator()
	{
		return new LanguageEntityIterator( $this );
	}
}
