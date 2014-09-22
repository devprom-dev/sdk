<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/ReleaseMetricsExtPersister.php";

class ReleaseModelMetricsBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Release ) return;
    	
 	    $object->addPersister( new ReleaseMetricsExtPersister() );
    }
}