<?php

include "PriorityIterator.php";

class Priority extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('Priority');
 		
 		$this->setSortDefault( new SortOrderedClause() );
 		$this->setAttributeDescription( 'RelatedColor', text(1853) );
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
 	}
 	
	function createIterator() 
	{
		return new PriorityIterator($this);
	}

    function getPage()
    {
        return '/admin/dictionaries.php?dict=Priority';
    }

    function modify_parms($id, $parms)
    {
        if ( $parms['IsDefault'] == 'Y' ) {
            $registry = $this->getRegistryBase();
            $objectIt = $registry->Query();
            while( !$objectIt->end() ) {
                $registry->Store($objectIt, array(
                    'IsDefault' => 'N'
                ));
                $objectIt->moveNext();
            }
        }
        return parent::modify_parms($id, $parms);
    }
}
