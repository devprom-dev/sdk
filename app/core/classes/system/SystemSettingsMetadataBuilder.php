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
        $metadata->addAttribute( 'ProxyServer', 'VARCHAR', text(2487), true, false, text(2489) );
        $metadata->addAttribute( 'ProxyAuth', 'VARCHAR', text(2488), true, false, text(2490) );

        if ( defined('AUTO_UPDATE') ) {
            $metadata->addAttribute( 'AutoUpdate', 'CHAR', text(2499), true, false );
        }

        $metadata->setAttributeRequired( 'OrderNum', false );
        $metadata->setAttributeCaption( 'AllowToChangeLogin', text(1345) );
        $metadata->setAttributeDescription( 'AdminEmail', text(1375) );
        $metadata->addAttribute( 'PlantUMLServer', 'VARCHAR', text(3123), true, false);
        $metadata->setAttributeEditable('PlantUMLServer', false);
        $metadata->addAttribute( 'MathJaxServer', 'VARCHAR', text(3124), true, false);
        $metadata->setAttributeEditable('MathJaxServer', false);
        $metadata->addAttribute( 'Parameters', 'TEXT', text(1078), true );
    }
}