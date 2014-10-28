<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorsDictionary.php";

class RequirementTypeForm extends PMPageForm
{
 	function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();
 		
 		$validator->addValidator( new ModelValidatorUnique(array('ReferenceName')) );
 		
 		return $validator;
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
