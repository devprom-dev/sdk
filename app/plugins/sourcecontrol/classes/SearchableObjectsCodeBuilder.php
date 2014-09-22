<?php

include_once SERVER_ROOT_PATH."pm/classes/common/SearchableObjectsBuilder.php";

class SearchableObjectsCodeBuilder extends SearchableObjectsBuilder
{
    public function build ( SearchableObjectRegistry $set )
    {
        $set->add( 'SubversionRevision', array('Description') );
    }
}