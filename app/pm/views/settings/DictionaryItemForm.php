<?php

class DictionaryItemForm extends PMPageForm
{
 	function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();

 		if ( $this->getObject() instanceof RequestType )
 		{
 			$validator->addValidator( new ModelValidatorUnique(array('ReferenceName')) );
 		}
 		
 		return $validator;
 	}
 	
 	function getFieldValue( $attr )
 	{
 		switch($attr)
 		{
 		    case 'HasIssues':
 		    	$value = parent::getFieldValue( $attr );
 		    	return $value == '' ? 'Y' : $value;
 		    default:
 		    	return parent::getFieldValue( $attr );
 		}
 	}
}