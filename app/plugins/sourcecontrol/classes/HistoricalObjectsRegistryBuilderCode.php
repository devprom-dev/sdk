<?php

include_once SERVER_ROOT_PATH."pm/classes/common/HistoricalObjectsRegistryBuilder.php";

class HistoricalObjectsRegistryBuilderCode extends HistoricalObjectsRegistryBuilder
{
    public function build ( HistoricalObjectsRegistry & $registry )
    {
 		$registry->add( 'SubversionRevision', array (
			'Author',
			'Repository'
		));
    }
}