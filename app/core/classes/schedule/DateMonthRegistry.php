<?php

class DateMonthRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
 		$this->items = array();
 		
		array_push($this->items, array('Caption' => 'ßíâàğü'));
		array_push($this->items, array('Caption' => 'Ôåâğàëü'));
		array_push($this->items, array('Caption' => 'Ìàğò'));
		array_push($this->items, array('Caption' => 'Àïğåëü'));
		array_push($this->items, array('Caption' => 'Ìàé'));
		array_push($this->items, array('Caption' => 'Èşíü'));
		array_push($this->items, array('Caption' => 'Èşëü'));
		array_push($this->items, array('Caption' => 'Àâãóñò'));
		array_push($this->items, array('Caption' => 'Ñåíòÿáğü'));
		array_push($this->items, array('Caption' => 'Îêòÿáğü'));
		array_push($this->items, array('Caption' => 'Íîÿáğü'));
		array_push($this->items, array('Caption' => 'Äåêàáğü'));
 		
 		foreach ( array_keys($this->items) as $key )
 		{
			$this->items[$key]['entityId'] = $key + 1;
 			$this->items[$key]['Caption'] = translate($this->items[$key]['Caption']); 
 		}
 		
 		return $this->createIterator( $this->items );
	}
}