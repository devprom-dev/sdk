<?php
include_once "TagBase.php";
include "TagIterator.php";
include "TagRegistry.php";
include "predicates/WorkItemTagFilter.php";

class Tag extends TagBase
{
 	function __construct( $entity_refname = 'Tag' )
 	{
 		parent::__construct(
 		    $entity_refname, $entity_refname == 'Tag' ? new TagRegistry($this) : new ObjectRegistrySQL($this)
        );
        $this->setSortDefault(
            array(
                new SortAttributeClause('Caption')
            )
        );
 	}
 	
 	function createIterator() {
 		return new TagIterator($this);
 	}
 	
 	function getGroupKey()
 	{
 	}

    function getPage() {
		return getSession()->getApplicationUrl($this).'project/tags?';
	}
}