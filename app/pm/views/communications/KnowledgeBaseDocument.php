<?php

include "KnowledgeBaseDocumentList.php";

class KnowledgeBaseDocument extends PMWikiDocument
{
    function buildDocumentIt()
	{
	    $document_it = $this->getObject()->getRootIt();
	    
	    if ( $document_it->getId() < 1 ) {
	    	$document_it = $this->getObject()->createCachedIterator( array (
                array (
                    'WikiPageId' => 0
                )
	    	));
		}
	     
	    return $document_it;
	}
	
    function getDefaultPagesNumber() {
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

	function getCompareToActions() {
        return array();
    }

	function getList( $type = '', $iterator = null ) {
	    return new KnowledgeBaseDocumentList( $this->getObject(), $iterator );
	}
	
	function getFilters()
	{
		$filters = PMPageTable::getFilters();

        $filters[] = new ViewSubmmitedAfterDateWebMethod();
        $filters[] = new ViewSubmmitedBeforeDateWebMethod();
        $filters[] = new ViewModifiedAfterDateWebMethod();
        $filters[] = new ViewModifiedBeforeDateWebMethod();

		$filters[] = $this->buildTagsFilter();
		$filters[] = new FilterObjectMethod(
				$this->getObject()->getAttributeObject('Author'), 
				translate($this->getObject()->getAttributeUserName( 'Author' )) 
			);

		return $filters;
	}

	function getActions()
	{
        $actions = array();
        $actions[] = array(
            'name' => text(1373),
            'url' => getFactory()->getObject('Module')->getExact('attachments')->getUrl('class=ProjectPage')
        );

        $actions[] = array();
		$actions = array_merge($actions, parent::getActions());

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( !getSession()->getProjectIt()->IsPortfolio() && $method->hasAccess() )
        {
            $actions['import'] = array(
                'name' => translate('Импортировать'),
                'url' => $method->getJSCall(array('view' => 'importdoc'), translate('Импорт'))
            );
            $actions[] = array();
        }

		return $actions;
	}

    function getDocumentsModuleIt() {
        return getFactory()->getObject('Module')->getEmptyIterator();
    }

    function getListViewWidgetIt() {
        return getFactory()->getObject('PMReport')->getExact('knowledgebaselist');
    }

    function getRenderParms($parms)
    {
        return array_merge(
            parent::getRenderParms($parms),
            array(
                'registry_title' => translate('Новости')
            )
        );
    }
}