<?php
include "WikiPageBaselineIterator.php";
include "WikiPageBaselineRegistry.php";
include "predicates/WikiPageBaselineUIDPredicate.php";
include "persisters/WikiPageBaselineDocumentPersister.php";
include "persisters/WikiPageBaselinePersister.php";

class WikiPageBaseline extends Metaobject
{
	public function __construct() {
		parent::__construct('cms_Snapshot', new WikiPageBaselineRegistry($this) );
        $this->addPersister(new WikiPageBaselinePersister());
		$this->setSortDefault(
		    array(
		        new SortAttributeClause('Caption')
            )
        );
	}
	
	public function getDisplayName() {
		return translate('Бейзлайн');
	}
	
	public function getObjectClass() {
		return '';
	}

    function createIterator() {
        return new WikiPageBaselineIterator($this);
    }

    function getPage() {
	    return '';
    }
}