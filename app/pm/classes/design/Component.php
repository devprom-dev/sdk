<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectHierarchyPersister.php";
include "ComponentIterator.php";

class Component extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::__construct('pm_Component', $registry);
 		$this->setSortDefault( array(
 		    new SortObjectHierarchyClause(),
            new SortAttributeClause('Caption')
        ));
 	}

	function createIterator() {
		return new ComponentIterator( $this );
	}

	function getValidators() {
        return array(
            new ModelValidatorAvoidInfiniteLoop()
        );
    }

    function getPage() {
		return getSession()->getApplicationUrl($this).'components/list?';
	}
	
	function IsDeletedCascade( $object ) {
		return false;
	}
}