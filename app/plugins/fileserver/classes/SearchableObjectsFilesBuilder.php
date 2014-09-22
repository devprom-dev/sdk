<?php

include_once SERVER_ROOT_PATH."pm/classes/common/SearchableObjectsBuilder.php";

class SearchableObjectsFilesBuilder extends SearchableObjectsBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( SearchableObjectRegistry $set )
    {
    	if( $this->session->getProjectIt()->get('IsFileServer') != 'Y' ) return;
    	
        $set->add( 'Artefact', array('Caption', 'Description') );
    }
}