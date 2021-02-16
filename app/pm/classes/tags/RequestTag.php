<?php
include_once "TagBase.php";
include "RequestTagIterator.php";
include "persisters/RequestTagCaptionPersister.php";

class RequestTag extends TagBase
{
 	function __construct()
 	{
 		parent::__construct('pm_RequestTag');

		$this->addAttribute('Caption', 'TEXT', translate('Название'), false);
		$this->addAttribute('ItemCount', 'INTEGER', translate('Количество'), false);
		$this->addPersister( new RequestTagCaptionPersister() );
        $this->addPersister( new TagParentPersister() );
        $this->setSortDefault(
            array(
                new TagCaptionSortClause()
            )
        );
 	}

 	function getPageNameObject( $object_id = '' ) {
 		return getFactory()->getObject('PMReport')->getExact('allissues')->getUrl('&state=all&tag='.$object_id);
 	}

	function createIterator() {
		return new RequestTagIterator($this);
	}
	
	function getGroupKey() {
		return 'Request';
	}
}