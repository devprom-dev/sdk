<?php
include_once SERVER_ROOT_PATH."pm/classes/search/SearchableObjectsBuilder.php";

class SearchableObjectsIntegrationBuilder extends SearchableObjectsBuilder
{
    public function build ( SearchableObjectRegistry $set )
    {
 		$set->add( 'RequestIntegration', array('ExternalLink'), 'allissues' );
   		$set->add( 'TaskIntegration', array('ExternalLink'), 'currenttasks' );
    }
}