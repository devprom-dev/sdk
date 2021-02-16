<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class IterationModelVelocityBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof Iteration ) return;
    
        $strategy = getSession()->getProjectIt()->getMethodologyIt()->getIterationEstimationStrategy();
        $object->addAttribute( 'Velocity', 'INTEGER', preg_replace('/:|\%1/', '', $strategy->getVelocityText($object)), false );
    }
}