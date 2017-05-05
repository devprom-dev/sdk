<?php
include_once "IntegrationApplicationRegistry.php";

class IntegrationTrackerRegistry extends IntegrationApplicationRegistry
{
	public function getAll()
    {
        return $this->createIterator(
            array_values(
                array_filter(
                    parent::getAll()->getRowset(),
                    function ($row) {
                        return $row['Type'] == 'tracker';
                    }
                )
            )
        );
    }
}