<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/IterationMetricsExtPersister.php";

class IterationModelMetricsBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Iteration ) return;
    	
 	    $object->addPersister( new IterationMetricsExtPersister() );
    }
}