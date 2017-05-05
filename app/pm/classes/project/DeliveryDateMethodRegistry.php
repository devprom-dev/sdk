<?php

class DeliveryDateMethodRegistry extends ObjectRegistrySQL
{
	function createSQLIterator($sql)
	{
        return $this->createIterator(
            array (
                array (
                    'entityId' => "1",
                    'Caption' => text(2311)
                ),
                array (
                    'entityId' => "2",
                    'Caption' => text(2312)
                ),
                array (
                    'entityId' => "3",
                    'Caption' => text(2313)
                ),
                array (
                    'entityId' => "4",
                    'Caption' => text(2314)
                ),
                array (
                    'entityId' => "5",
                    'Caption' => text(2315)
                ),
                array (
                    'entityId' => "6",
                    'Caption' => text(2316)
                )
            )
        );
	}
}