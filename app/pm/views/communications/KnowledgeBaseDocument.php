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
		
		$filters[] = new FilterAutoCompleteWebMethod( 
				$this->getObject()->getAttributeObject('Author'), 
				translate($this->getObject()->getAttributeUserName( 'Author' )) 
			);
		
		return $filters;
	}

	function getFiltersDefault()
	{
		return array('tag');
	}
	
	function getNewActions()
	{
		if ( !getFactory()->getAccessPolicy()->can_create($this->getObject()) ) return array();
		
		$actions = array();
		
		$url = $this->object->getPageNameObject();
		
		if ( $this->getDocumentIt()->getId() > 0 )
		{
			$url .= '&ParentPage='.$this->getDocumentIt()->getId();
		}
		
		$actions['create'] = array( 
	        'name' => translate('Раздел'),
			'url' => $url,
			'uid' => 'create'
		);
		
		return $actions;
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
		
   		$actions[] = array();
    		
        $actions[] = array( 
			'name' => translate('Импортировать'),
			'url' => '?view=import&mode=xml&object=projectpage'
		);
		
		return $actions;
	}
}