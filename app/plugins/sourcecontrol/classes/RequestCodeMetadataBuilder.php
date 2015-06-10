<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include 'persisters/RequestPersister.php';

class RequestCodeMetadataBuilder extends ObjectMetadataEntityBuilder 
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}

    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	if ( $this->session->getProjectIt()->getMethodologyIt()->get('IsSubversionUsed') != 'Y' ) return;

		$orderNum = $metadata->getAttributeOrderNum('Links') > 0 
 			? $metadata->getAttributeOrderNum('Links') : $metadata->getLatestOrderNum();
    	
        $metadata->addAttribute( 'SourceCode', 'REF_pm_SubversionRevisionId', translate('Исходный код'), true, false, '', $orderNum + 50 );
        $metadata->addPersister( new RequestSourceCodePersister() );
        $metadata->addAttributeGroup('SourceCode', 'trace');
    }
}
