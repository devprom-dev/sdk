<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ReleaseModelVelocityBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof Release ) return;

        $strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
        $object->addAttribute( 'Velocity', 'INTEGER', preg_replace('/:|\%1/', '', $strategy->getVelocityText($object)), false );
        $object->setAttributeCaption( 'Caption', translate('Релиз') );
    }
}