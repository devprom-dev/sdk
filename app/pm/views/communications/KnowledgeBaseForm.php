<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/PMWikiForm.php";

class KnowledgeBaseForm extends PMWikiForm
{
	function getAppendActionName()
	{
		return translate('Статья');
	}

 	function getTraceActions( $page_it )
	{
		return array();
	}
	
	function IsAttributeVisible( $attr )
	{
	    $object_it = $this->getObjectIt();
	
	    switch( $attr )
	    {
	        case 'Template':
	            return !is_object($object_it);
	
	        default:
	            return parent::IsAttributeVisible( $attr );
	    }
	}

	function IsAttributeEditable($attr_name)
	{
		$object_it = $this->getObjectIt();
		switch($attr_name) {
			case 'Caption':
				if ( $this->getReviewMode() && is_object($object_it) && $object_it->get('ParentPage') == '' ) {
					return false;
				}
				break;
		}
		return parent::IsAttributeEditable($attr_name);
	}
}