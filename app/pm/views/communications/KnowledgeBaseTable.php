<?php

include "KnowledgeBaseList.php";

class KnowledgeBaseTable extends PMWikiTable
{
	function relatesToBacklog()
	{
		return false;
	}
	
	function getFilters()
	{
		$object = $this->getObject();
		
		$filters = array(
			new ViewWikiModifiedAfterDateWebMethod(),
			$this->buildTagsFilter()
			);
			
		return $filters;
	}

	function getFilterPredicates()
    {
        return array_merge(
            parent::getFilterPredicates(),
            array(
                new FilterAttributeNotNullPredicate('ParentPage')
            )
        );
    }

    function getList( $mode = '' )
	{
		switch ( $mode )
		{
			case '':
			case 'list':
			case 'tree':
			    return new KnowledgeBaseList( $this->getObject() );

			default:
		 		return parent::getList( $mode );
		}
	}

    function getNewPageTitle()
    {
        return translate('Статья');
    }
}
