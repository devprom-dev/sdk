<?php

include_once SERVER_ROOT_PATH."pm/classes/common/SharedObjectsBuilder.php";

class SharedObjectsSourceCodeBuilder extends SharedObjectsBuilder
{
    public function getGroup()
    {
        return 'SourceCode';
    }
    
    public function checkSharedInProject( $project_it )
    {
        return $project_it->get('IsSubversionUsed') == 'Y';
    }
    
    public function build( SharedObjectRegistry & $set )
    {
        $set->add( 'Subversion', $this->getGroup() );
        $set->add( 'SubversionRevision', $this->getGroup() );
        $set->add( 'RequestTraceSourceCode', $this->getGroup() );
        $set->add( 'TaskTraceSourceCode', $this->getGroup() );
    }
}