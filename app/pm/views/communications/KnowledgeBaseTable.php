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
			new ViewWikiTagWebMethod( $object )
			);
			
		return $filters;
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
}
