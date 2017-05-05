<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class SystemSettingsMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'cms_SystemSettings' ) return;

        $metadata->addAttribute( 'ServerName', 'VARCHAR', text(1076), true, false, text(1218) );
        $metadata->addAttribute( 'ServerPort', 'VARCHAR', text(1151), true, false, text(465) );
        $metadata->addAttribute( 'TimeZoneUTC', 'VARCHAR', text(2026), true, false, text(2027) );
        $metadata->addAttribute( 'PasswordLength', 'INTEGER', text(2070), true, false, text(2071), 60 );
        $metadata->addAttribute( 'Parameters', 'TEXT', text(1078), true );

        $metadata->setAttributeRequired( 'OrderNum', false );
        $metadata->setAttributeCaption( 'AllowToChangeLogin', text(1345) );
        $metadata->setAttributeDescription( 'AdminEmail', text(1375) );
    }
}