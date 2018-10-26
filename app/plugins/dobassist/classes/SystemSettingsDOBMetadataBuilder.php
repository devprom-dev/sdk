<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class SystemSettingsDOBMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'cms_SystemSettings' ) return;

 		$metadata->setAttributeVisible("ServerName", false);
        $metadata->setAttributeVisible("ServerPort", false);
        $metadata->setAttributeVisible("TimeZoneUTC", false);
        $metadata->setAttributeVisible("Parameters", false);
    }
}
