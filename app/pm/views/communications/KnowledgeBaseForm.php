<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/PMWikiForm.php";

class KnowledgeBaseForm extends PMWikiForm
{
 	function __construct( $object, $template_object ) {
		parent::__construct( $_REQUEST['IsTemplate'] > 0 ? $template_object : $object, $template_object );
 	}

	function extendModel()
	{
		parent::extendModel();

		if ( $_REQUEST['IsTemplate'] < 1 ) {
			$this->object->addAttribute('Template', '', translate('Шаблон'), false, false, '', 15);
		}
	}

	function getAppendActionName()
	{
		return translate('Статья');
	}

	function getTemplatesModule() {
		return 'kbtemplates';
	}

 	function getTraceActions( $page_it )
	{
		return array();
	}
	
	function getActions( $page_it = null )
	{
		if ( !is_object($page_it) ) $page_it = $this->getObjectIt();
		if ( !is_object($page_it) ) return array();
		
		$actions = parent::getActions( $page_it );

		$export_actions = $this->getExportActions( $page_it );
		if ( count($export_actions) > 0 )
		{
    		array_push($actions, array( '' ) );
    		$actions[] = array(
    		    'name' => translate('Экспорт'),
    			'items' => $export_actions
    		);
		}
		
		return $actions;
	}
	
	function drawCaption()
	{
		$object_it = $this->getObjectIt();
		
		if ( is_object($object_it) && $object_it->get('ParentPage') < 1 )
		{
			return;
		}
		
		parent::drawCaption();
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
				if ( $this->getReviewMode() && is_object($object_it) && $object_it->get('ParentPage') == '' && $object_it->get('IsTemplate') < 1 ) {
					return false;
				}
				break;
		}
		return parent::IsAttributeEditable($attr_name);
	}
}