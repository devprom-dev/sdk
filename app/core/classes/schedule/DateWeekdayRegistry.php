<?php

class DateWeekdayRegistry extends ObjectRegistrySQL
{
 	function getAll()
 	{
 		$this->items = array();
 		
		array_push($this->items, array('Caption' => '�����������'));
		array_push($this->items, array('Caption' => '�����������'));
		array_push($this->items, array('Caption' => '�������'));
		array_push($this->items, array('Caption' => '�����'));
		array_push($this->items, array('Caption' => '�������'));
		array_push($this->items, array('Caption' => '�������'));
		array_push($this->items, array('Caption' => '�������'));
 		
 		foreach ( array_keys($this->items) as $key )
 		{
 			$this->items[$key]['entityId'] = $key;
 			$this->items[$key]['Caption'] = translate($this->items[$key]['Caption']); 
 		}
 		
 		return $this->createIterator( $this->items );
 	}
}
