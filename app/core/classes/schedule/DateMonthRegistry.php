<?php

class DateMonthRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
 		$this->items = array();
 		
		array_push($this->items, array('Caption' => 'Январь'));
		array_push($this->items, array('Caption' => 'Февраль'));
		array_push($this->items, array('Caption' => 'Март'));
		array_push($this->items, array('Caption' => 'Апрель'));
		array_push($this->items, array('Caption' => 'Май'));
		array_push($this->items, array('Caption' => 'Июнь'));
		array_push($this->items, array('Caption' => 'Июль'));
		array_push($this->items, array('Caption' => 'Август'));
		array_push($this->items, array('Caption' => 'Сентябрь'));
		array_push($this->items, array('Caption' => 'Октябрь'));
		array_push($this->items, array('Caption' => 'Ноябрь'));
		array_push($this->items, array('Caption' => 'Декабрь'));
 		
 		foreach ( array_keys($this->items) as $key )
 		{
			$this->items[$key]['entityId'] = $key + 1;
 			$this->items[$key]['Caption'] = translate($this->items[$key]['Caption']); 
 		}
 		
 		return $this->createIterator( $this->items );
	}
}