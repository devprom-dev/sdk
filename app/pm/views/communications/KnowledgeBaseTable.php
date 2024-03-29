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
		$filters = array(
			$this->buildTagsFilter()
			);
        $filters[] = new ViewSubmmitedAfterDateWebMethod();
        $filters[] = new ViewSubmmitedBeforeDateWebMethod();
        $filters[] = new ViewModifiedAfterDateWebMethod();
        $filters[] = new ViewModifiedBeforeDateWebMethod();

		return $filters;
	}

	function getFilterPredicates( $values )
    {
        return array_merge(
            parent::getFilterPredicates( $values ),
            array(
                new FilterAttributeNotNullPredicate('ParentPage')
            )
        );
    }

    function getList( $mode = '', $iterator = null )
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
