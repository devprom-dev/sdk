<?php

include_once SERVER_ROOT_PATH."pm/classes/search/SearchableObjectsBuilder.php";

class SearchableObjectsFilesBuilder extends SearchableObjectsBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( SearchableObjectRegistry $set )
    {
    	if( $this->session->getProjectIt()->getMethodologyIt()->get('IsFileServer') != 'Y' ) return;
    	
        $set->add( 'Artefact', array('Caption', 'Description') );
    }
}