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
        getSession()->addBuilder( new WikiPageModelExtendedBuilder() );
        getSession()->addBuilder( new KnowledgeBaseSettingBuilder() );
        getSession()->addBuilder( new KnowledgeBaseDocumentSettingBuilder() );

 		parent::__construct();
 	}
 	
 	function getObject() {
		return new ProjectPage();
	}
	
	function getBulkForm() {
		return new WikiBulkForm($this->getObject());
	}
	
	function getEntityForm() {
       	return new KnowledgeBaseForm($this->getObject());
 	}
 	
 	function getDocumentTableBase( $object ) {
 		return new KnowledgeBaseDocument($object, $this->getStateObject(), $this->getForm());
 	}
 	
 	function getTableBase() {
        return new KnowledgeBaseTable( $this->getObject(), $this->getStateObject(), $this->getForm() );
 	}

 	function getTable() {
 	    if ( $this->getReportBase() == 'knowledgebaselist' ) {
            return $this->getTableBase();
        }
        return parent::getTable();
    }

    function exportWikiTree()
    {
        $rootIds = TextUtils::parseIds($_REQUEST['root']);

        if ( count($rootIds) > 1 ) {
            if ( $_REQUEST['lazyroot'] > 0 ) {
                $_REQUEST['root'] = $_REQUEST['lazyroot'];
                $rootJson = array_shift(parent::exportWikiTree());
                return $rootJson['children'];
            }
            $object_it = $this->getObject()->getExact($rootIds);
            while( !$object_it->end() ) {
                $json[] = $this->exportRootNode($object_it);
                $object_it->moveNext();
            }
            return $json;
        }
        else {
            return parent::exportWikiTree();
        }
    }

    function exportRootNode( $object_it )
    {
        return array (
            'title' => $object_it->getRef('Project')->getDisplayName(),
            'folder' => true,
            'key' => $object_it->getId(),
            'expanded' => false,
            'extraClasses' => 'folder',
            'lazy' => true,
            'data' => array(
                'project' => $object_it->get('ProjectCodeName')
            )
        );
    }
}