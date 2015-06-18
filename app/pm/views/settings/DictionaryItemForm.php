<?php

class DictionaryItemForm extends PMPageForm
{
 	function extendModel()
 	{
 	 	if ( $this->getObject() instanceof Environment )
 		{
 			$this->getObject()->setAttributeVisible('OrderNum', false);
 			$this->getObject()->setAttributeVisible('IncidentsCount', false);
 			$this->getObject()->setAttributeVisible('Issues', false);
 			$this->getObject()->setAttributeVisible('RecentComment', false);
 		}
 		
 		parent::extendModel();
 	}
 	
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