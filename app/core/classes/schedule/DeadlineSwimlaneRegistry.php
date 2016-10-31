<?php

class DeadlineSwimlaneRegistry extends ObjectRegistrySQL
{
 	function getAll()
 	{
 		return $this->createIterator(
 				array (
 						array('entityId' => 1, 'Caption' => text(1891)),
 						array('entityId' => 2, 'Caption' => text(1892)),
 						array('entityId' => 3, 'Caption' => text(1893)),
 						array('entityId' => 4, 'Caption' => text(1894)),
 						array('entityId' => 5, 'Caption' => text(1895)),
 						array('entityId' => 6, 'Caption' => text(1896)),
 						array('entityId' => 7, 'Caption' => text(2245))
 				)
 		);
 	}
}
