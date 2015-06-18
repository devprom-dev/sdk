<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include 'persisters/TaskPersister.php';

class TaskCodeMetadataBuilder extends ObjectMetadataEntityBuilder 
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}

    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Task' ) return;
    	if ( $this->session->getProjectIt()->getMethodologyIt()->get('IsSubversionUsed') != 'Y' ) return;
    	
		$metadata->addAttribute( 'SourceCode', 'REF_pm_SubversionRevisionId', translate('Исходный код'), true );
		$metadata->addPersister( new TaskSourceCodePersister() );
        $metadata->addAttributeGroup('SourceCode', 'trace');
    }
}
