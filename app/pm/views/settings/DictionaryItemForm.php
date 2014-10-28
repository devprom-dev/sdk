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
}