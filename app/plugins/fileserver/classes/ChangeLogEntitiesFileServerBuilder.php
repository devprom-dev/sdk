<?php

include_once SERVER_ROOT_PATH."core/classes/history/ChangeLogEntitiesBuilder.php";

class ChangeLogEntitiesFileServerBuilder extends ChangeLogEntitiesBuilder
{
    public function build( ChangeLogEntityRegistry $set )
    {
        $entities = array (
 			'pm_Artefact'
 		);

        foreach( $entities as $entity )
        {
            $set->add( $entity );
        }
    }
}