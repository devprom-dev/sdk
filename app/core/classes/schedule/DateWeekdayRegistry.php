<?php

class DateWeekdayRegistry extends ObjectRegistrySQL
{
 	function Query($parms = array())
 	{
 		$this->items = array();
 		
		array_push($this->items, array('Caption' => 'Воскресенье'));
		array_push($this->items, array('Caption' => 'Понедельник'));
		array_push($this->items, array('Caption' => 'Вторник'));
		array_push($this->items, array('Caption' => 'Среда'));
		array_push($this->items, array('Caption' => 'Четверг'));
		array_push($this->items, array('Caption' => 'Пятница'));
		array_push($this->items, array('Caption' => 'Суббота'));
 		
 		foreach ( array_keys($this->items) as $key )
 		{
 			$this->items[$key]['entityId'] = strval($key);
 			$this->items[$key]['Caption'] = translate($this->items[$key]['Caption']); 
 		}
 		
 		return $this->createIterator( $this->items );
 	}
}
