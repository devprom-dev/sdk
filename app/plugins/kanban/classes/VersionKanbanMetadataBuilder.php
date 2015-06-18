<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class VersionKanbanMetadataBuilder extends ObjectMetadataEntityBuilder 
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
		
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Version' ) return;

    	if ( $this->session->getProjectIt()->getMethodologyIt()->get('IsKanbanUsed') != 'Y' ) return;
    	
 		$metadata->addAttribute( 'Progress', '', translate('Прогресс'), false, false, '', 80 );
    }
}
