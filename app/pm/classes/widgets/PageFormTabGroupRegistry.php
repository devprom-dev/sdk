<?php

class PageFormTabGroupRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	public function add( $group, $title )
	{
		$this->data[] = array (
            'ReferenceName' => $group,
            'Caption' => $title,
            'entityId' => $group
		);
	}

 	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('PageFormTabGroupBuilder') as $builder ) {
 			$builder->build($this);
 		}
        return $this->createIterator( $this->data );
 	}
}