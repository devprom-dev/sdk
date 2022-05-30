<?php

class DeadlineSwimlaneRegistry extends ObjectRegistrySQL
{
 	function Query($parms = array())
 	{
        $now = strtotime(SystemDateTime::date());
 		return $this->createIterator(
 				array (
 						array(
 						    'entityId' => 1,
                            'Caption' => text(1891),
                            'ReferenceName' => strftime('%Y-%m-%d', strtotime('-1 days', $now)),
                            'Days' => 0
                        ),
 						array(
 						    'entityId' => 2,
                            'Caption' => text(1892),
                            'ReferenceName' => strftime('%Y-%m-%d', strtotime('1 days', $now)),
                            'Days' => 1
                        ),
 						array(
 						    'entityId' => 3,
                            'Caption' => text(1893),
                            'ReferenceName' => strftime('%Y-%m-%d', strtotime('7 days', $now)),
                            'Days' => 7
                        ),
 						array(
 						    'entityId' => 4,
                            'Caption' => text(1894),
                            'ReferenceName' => strftime('%Y-%m-%d', strtotime('14 days', $now)),
                            'Days' => 14
                        ),
 						array(
 						    'entityId' => 6,
                            'Caption' => text(1896),
                            'ReferenceName' => strftime('%Y-%m-%d', strtotime('1 month', $now)),
                            'Days' => 30
                        ),
 						array(
 						    'entityId' => 7,
                            'Caption' => text(2245),
                            'ReferenceName' => '',
                            'Days' => ''
                        )
 				)
 		);
 	}
}
