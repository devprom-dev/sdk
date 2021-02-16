<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class StageMetricsModelBuilder extends ObjectModelBuilder
{
    private $methodologyIt = null;

    function __construct( $methodologyIt ) {
        $this->methodologyIt = $methodologyIt;
    }

    public function build( Metaobject $object )
    {
        if ( !$this->methodologyIt->IsAgile() ) return;

        $object->addAttribute('ActualVelocity', 'INTEGER', text(2322), true, false, '', 100);
        $object->addAttribute('LeftDuration', 'INTEGER', text(1422), true, false, '', 120);
        $object->addAttribute('Capacity', 'INTEGER', text(1020), true, false, '', 130);
        $object->addAttribute('LeftVolume', 'INTEGER', text(2693), true, false, '', 140);

        foreach( array('ActualVelocity','LeftDuration','Capacity','LeftVolume') as $attribute ) {
            $object->setAttributeEditable($attribute, false);
            $object->addAttributeGroup($attribute, 'single-row-bottom');
        }
    }
}