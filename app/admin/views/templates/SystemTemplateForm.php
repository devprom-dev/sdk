<?php

class SystemTemplateForm extends AdminPageForm
{
	function buildRelatedDataCache()
	{
		parent::buildRelatedDataCache();
		
		foreach( $this->getObject()->getAttributes() as $attribute => $data )
		{
			$this->getObject()->setAttributeVisible($attribute, false);
		}
		$this->getObject()->setAttributeVisible('Content', true);
	}
	
	function createField( $attr )
	{
		$field = parent::createField($attr);
		
		switch($attr)
		{
		    case 'Content':
		    	$field->setRows(40);
		    	break;
		}
		
		return $field;
	}
	
	function getDeleteActions()
	{
	    $actions = array();
	    
	    $object_it = $this->getObjectIt();
		if ( !is_object($object_it) ) return $actions;
		
		$method = new DeleteObjectWebMethod($object_it);
		if ( $method->hasAccess() && file_exists($object_it->getFilePath()) )
		{
			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
		    $actions[] = array(
			    'name' => text(2039), 'url' => $method->getJSCall() 
		    );
		}
		
		return $actions;
	}
}