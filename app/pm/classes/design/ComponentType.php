<?php
include "ComponentTypeIterator.php";

class ComponentType extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::__construct('pm_ComponentType', $registry);
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
        $this->setSortDefault( array(
            new SortAttributeClause('OrderNum')
        ));
 	}

	function createIterator() {
		return new ComponentTypeIterator( $this );
	}
}