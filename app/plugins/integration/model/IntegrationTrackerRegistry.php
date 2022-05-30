<?php
include_once "IntegrationApplicationRegistry.php";

class IntegrationTrackerRegistry extends IntegrationApplicationRegistry
{
	public function Query($parms = array())
    {
        return $this->createIterator(
            array_values(
                array_filter(
                    parent::Query($parms)->getRowset(),
                    function ($row) {
                        return $row['Type'] == 'tracker';
                    }
                )
            )
        );
    }
}