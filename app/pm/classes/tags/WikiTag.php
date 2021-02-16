<?php
include_once "TagBase.php";
include "WikiTagIterator.php";
include "predicates/WikiTagReferenceFilter.php";
include "persisters/WikiTagCaptionPersister.php";

class WikiTag extends TagBase
{
 	function __construct() 
 	{
 		parent::__construct('WikiTag');

		$this->addAttribute('Caption', 'TEXT', translate('Название'), false);
		$this->addAttribute('ItemCount', 'INTEGER', translate('Количество'), false);
		$this->addPersister( new WikiTagCaptionPersister() );
        $this->addPersister( new TagParentPersister() );
        $this->setSortDefault(
            array(
                new TagCaptionSortClause()
            )
        );
 	}
 	
	function createIterator() {
		return new WikiTagIterator($this);
	}
 	
	function getGroupKey() {
		return 'Wiki';
	}

 	function getPageNameObject( $object_id = '' ) {
 		return '?state=all&tag='.$object_id;
 	}
}