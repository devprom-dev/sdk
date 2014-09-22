<?php

class DateYearRegistry extends ObjectRegistrySQL
{
 	function getAll()
 	{
 		$this->items = array();
 		
 		for ( $i = 0; $i < 5; $i++ )
 		{
 			array_push($this->items, array('entityId' => $i, 'Caption' => date('Y') - $i));
 		}
 		
 		return $this->createIterator( $this->items );
 	}
}
