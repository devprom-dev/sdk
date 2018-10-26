<?php

include_once SERVER_ROOT_PATH."core/classes/history/ChangeLogEntitiesBuilder.php";

class ChangeLogEntitiesFeatureAnalysisBuilder extends ChangeLogEntitiesBuilder
{
    public function build( ChangeLogEntityRegistry $set )
    {
        $entities = array (
 			'pm_Competitor',
            'pm_FeatureAnalysis'
 		);

        foreach( $entities as $entity )
        {
            $set->add( $entity );
        }
    }
}