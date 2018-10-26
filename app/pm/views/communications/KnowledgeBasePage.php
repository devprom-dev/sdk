<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/PMWikiUserPage.php';
include_once SERVER_ROOT_PATH.'pm/views/wiki/WikiBulkForm.php';
 
include "KnowledgeBaseForm.php";
include "KnowledgeBaseTable.php";
include "KnowledgeBaseDocument.php";
include "KnowledgeBaseDocumentSettingBuilder.php";
include "KnowledgeBaseSettingBuilder.php";

class KnowledgeBasePage extends PMWikiUserPage
{
 	function __construct()
 	{
        if ( $_REQUEST['report'] != '' ) {
            $report_it = getFactory()->getObject('PMReport')->getExact($_REQUEST['report']);
            $report = is_numeric($report_it->getId()) ? $report_it->get('Report') : $report_it->getId();
        }

 		$b_show_root = !$this->needDisplayForm()
 			&& $_REQUEST['mode'] == '' && $_REQUEST['view'] == 'tree'
 			&& $_REQUEST['export'] == '' && !in_array($report, array('knowledgebaselist','latestarticles'));
 		
 		if ( $b_show_root )
 		{
 			$_REQUEST['view'] = 'docs';
 			$_REQUEST['document'] = '1';
 		}

 		parent::__construct();

        getSession()->addBuilder( new KnowledgeBaseSettingBuilder() );
 		getSession()->addBuilder( new KnowledgeBaseDocumentSettingBuilder() );
 	}
 	
 	function getPredicates()
 	{
 		return array( new KnowledgeBaseAccessPredicate() );
 	}
 	
 	function buildObject()
	{
		$object = getFactory()->getObject('ProjectPage');
		$builders = array (
			new WikiPageModelExtendedBuilder()
		);
		foreach( $builders as $builder ) {
			$builder->build($object);
		}

		foreach ( $this->getPredicates() as $predicate ) {
			$object->addFilter($predicate);
		}
		
		return $object;
	}
	
	function getBulkForm() {
		return new WikiBulkForm($this->getObject());
	}
	
	function getFormBase() {
       	return new KnowledgeBaseForm($this->getObject());
 	}
 	
 	function getDocumentTableBase( & $object ) {
 		return new KnowledgeBaseDocument($object, $this->getStateObject(), $this->getForm());
 	}
 	
 	function getTableBase() {
        return new KnowledgeBaseTable( $this->getObject(), $this->getStateObject(), $this->getForm() );
 	}

	function getExportPersisters() {
		return array_merge(
			parent::getExportPersisters(),
			array (
			)
		);
	}
}