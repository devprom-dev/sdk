<?php
include_once SERVER_ROOT_PATH."core/classes/history/ChangeLogEntitiesBuilder.php";

class ChangeLogEntitiesScrumBuilder extends ChangeLogEntitiesBuilder
{
    public function build( ChangeLogEntityRegistry $set )
    {
        $entities = array (
 			'Scrum'
 		);
        foreach( $entities as $entity ) {
            $set->add( $entity );
        }
    }
}