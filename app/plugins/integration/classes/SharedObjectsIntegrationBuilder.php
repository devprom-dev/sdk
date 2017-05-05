<?php
include_once SERVER_ROOT_PATH."pm/classes/common/SharedObjectsBuilder.php";

class SharedObjectsIntegrationBuilder extends SharedObjectsBuilder
{
    public function getGroup() {
        return 'Common';
    }
    
    public function build( SharedObjectRegistry & $set )
    {
        $set->add( 'Integration', $this->getGroup() );
    }
}