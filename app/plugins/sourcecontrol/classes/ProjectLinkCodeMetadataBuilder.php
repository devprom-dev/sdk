<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ProjectLinkCodeMetadataBuilder extends ObjectMetadataEntityBuilder 
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}

    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_ProjectLink' ) return;
    	if ( $this->session->getProjectIt()->getMethodologyIt()->get('IsSubversionUsed') != 'Y' ) return;
    	
    	$metadata->setAttributeVisible( 'SourceCode', true );
    }
}
