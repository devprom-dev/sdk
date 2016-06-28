<?php

include "KnowledgeBaseDocumentList.php";

class KnowledgeBaseDocument extends PMWikiDocument
{
    function buildDocumentIt()
	{
	    $document_it = $this->getObject()->getRootIt();
	    
	    if ( $document_it->getId() < 1 )
	    {
	    	$document_it = $this->getObject()->createCachedIterator( array (
	    			array (
	    					'WikiPageId' => 0
	    			)
	    	));
		}
	     
	    return $document_it;
	}
	
	function getPreviewPagesNumber()
	{
		return 1;
	}

	function getSectionName() {
		return translate('Страница');
	}

	function getNewActions()
	{
		if ( getSession()->getProjectIt()->IsPortfolio() ) return array();
		return parent::getNewActions();
	}

	function getList( $type = '', $iterator = null )
	{
	    $list = new KnowledgeBaseDocumentList( $this->getObject(), $iterator );
	    
	    $list->setInfiniteMode();
	    
	    return $list;
	}
	
	function getFilters()
	{
		$filters = array();
		
		$filters[] = new ViewWikiModifiedAfterDateWebMethod();
		$filters[] = new ViewWikiTagWebMethod( $this->getObject() );
		$filters[] = new FilterObjectMethod(
				$this->getObject()->getAttributeObject('Author'), 
				translate($this->getObject()->getAttributeUserName( 'Author' )) 
			);
		$filters[] = new FilterTextWebMethod( text(2087), 'search', 'width:380px;' );

		return $filters;
	}

	function getFiltersDefault()
	{
		return array('tag', 'search');
	}
	
	function getTraceActions()
	{
		$actions = parent::getTraceActions();
		
	    $actions[] = array( 
            'name' => text(1372),
		    'url' => getSession()->getApplicationUrl().'knowledgebase/tree?view=list'
        );
	    
		$actions[] = array( 
            'name' => text(1373),
		    'url' => getSession()->getApplicationUrl().'knowledgebase/tree?view=files'
        );
		
		return $actions;
	}
	
	function getActions()
	{
		$actions = parent::getActions();

		if ( !getSession()->getProjectIt()->IsPortfolio() ) {
			$actions[] = array();
			$actions[] = array(
				'name' => translate('Импортировать'),
				'url' => '?view=import&mode=xml&object=projectpage'
			);
		}

		return $actions;
	}
}