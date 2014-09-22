<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorsDictionary.php";

class RequirementTypeForm extends PMPageForm
{
	function __construct() 
	{
		global $model_factory;
		
		parent::__construct( $model_factory->getObject('RequirementType') );
	}

	function createFieldObject( $attr_name ) 
	{
		global $model_factory;
		
		switch( $attr_name )
		{
			case 'WikiEditor':
				
			    return new WikiEditorsDictionary();
				
			default:
				
			    return parent::createFieldObject( $attr_name );
		}
	}
}
