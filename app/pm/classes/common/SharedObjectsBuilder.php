<?php

class SharedObjectsBuilder
{
    public function getGroup()
    {
        return '';
    }
    
    function checkSharedInProject( $project_it )
    {
        return true;
    }
    
    public function build( SharedObjectRegistry & $set )
    {
    }
}