<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/PMWikiUserPage.php';
include_once SERVER_ROOT_PATH.'pm/views/wiki/WikiBulkForm.php';
 
include "KnowledgeBaseForm.php";
include "KnowledgeBaseTable.php";
include "KnowledgeBaseDocument.php";
include "KnowledgeBaseDocumentSettingBuilder.php";

class KnowledgeBasePage extends PMWikiUserPage
{
 	function __construct()
 	{
 		global $model_factory;
 		
 		$b_show_root = !$this->needDisplayForm() 
 			&& $_REQUEST['mode'] == '' && $_REQUEST['view'] == 'tree'
 			&& $_REQUEST['export'] == '';
 		
 		if ( $b_show_root )
 		{
 			$_REQUEST['view'] = 'docs';
 			$_REQUEST['document'] = '1';
 		}

 		parent::__construct();
 		
 		getSession()->addBuilder( new KnowledgeBaseDocumentSettingBuilder() );
 	}
 	
 	function getPredicates()
 	{
 		return array( new KnowledgeBaseAccessPredicate() );
 	}
 	
 	function getObject() 
	{
		$object = getFactory()->getObject('ProjectPage');
		
		foreach ( $this->getPredicates() as $predicate )
		{
			$object->addFilter($predicate);
		}
		
		return $object;
	}
	
	function getTemplateObject()
	{
		return getFactory()->getObject('KnowledgeBaseTemplate');
	}

	function getBulkForm()
	{
		return new WikiBulkForm( $this->getObject() );
	}
	
	function getFormBase()
 	{
	    switch ( $_REQUEST['view'] )
	    {
	        case 'templates':
	        	return new KnowledgeBaseForm( $this->getTemplateObject(), $this->getTemplateObject() );
	        	
	        default;
	        	return new KnowledgeBaseForm( $this->getObject(), $this->getTemplateObject() );
	    }
 	}
 	
 	function getDocumentTableBase( & $object )
 	{
 		return new KnowledgeBaseDocument($object, $this->getStateObject(), $this->getForm());
 	}
 	
 	function getTableBase() 
 	{
        return new KnowledgeBaseTable( $this->getObject(), $this->getStateObject(), $this->getForm() );
 	}
}