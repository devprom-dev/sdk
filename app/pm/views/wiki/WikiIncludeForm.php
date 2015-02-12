<?php

include_once SERVER_ROOT_PATH."pm/classes/wiki/validators/ModelValidatorIncludePage.php";
include_once "PMWikiForm.php";

class WikiIncludeForm extends PMPageForm
{
 	function __construct( $object ) 
	{
		parent::__construct( $object );
	}
    protected function extendModel()
    {
 		parent::extendModel();
 		
 		$object = $this->getObject();
 		
 		foreach( $object->getAttributes() as $attribute => $data )
 		{
 			$object->setAttributeVisible($attribute, false);
 			$object->setAttributeRequired($attribute, false);
 		}
 		$object->addAttribute('Include', 'INTEGER', '', false, false);
 		
 		if ( $this->getFieldValue('Include') == "1" )
 		{
 			$object->addAttribute('PageToInclude', 'REF_'.get_class($this->getObject()).'Id', text('testing55'), true, false, text('testing59'));
 			$object->setAttributeRequired('PageToInclude', true);
 		}
 		else
 		{
 			$object->setAttributeVisible('ParentPage', true);
 			$object->setAttributeRequired('ParentPage', true);
 			$object->setAttributeCaption('ParentPage', text('testing58'));
 			$object->setAttributeDescription('ParentPage', text('testing60'));
 		}
    }
    
 	function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();
 		$validator->insertValidator( new ModelValidatorIncludePage() );
 		return $validator;
 	}
    
	function createFieldObject( $name )
	{
		switch ( $name )
		{		
			case 'ParentPage':
			case 'PageToInclude':
				return new FieldHierarchySelector( $this->getObject()->getAttributeObject($name) );
				
			default:
				return parent::createFieldObject( $name );
		}
	}    
}