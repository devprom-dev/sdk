<?php

class SearchWordsModeRegistry extends ObjectRegistrySQL
{
    public function Query( $parms = array() )
 	{
 		return $this->createIterator( array(
            array(
                'entityId' => 'any',
                'Caption' => text(3207)
            ),
            array(
                'entityId' => 'all',
                'Caption' => text(3208)
            ),
            array(
                'entityId' => 'exact',
                'Caption' => text(3209)
            )
        ));
 	}
}
