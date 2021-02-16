<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class UserMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'cms_User' ) return;

        $metadata->addPersister( new UserReadonlyPersister() );

        $system_attributes = array ('IsShared', 'Rating', 'IsActivated', 'SessionHash', 'ICQ', 'Skype', 'PhotoExt', 'PhotoPath');
        foreach( $system_attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }
        $system_attributes = array ('Phone', 'Description', 'LDAPUID');
        foreach( $system_attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'additional');
        }

        foreach( array( 'ICQ', 'Skype' ) as $attribute ) {
            $metadata->setAttributeVisible( $attribute, false );
        }

        $metadata->setAttributeCaption( 'Phone', translate('Контакты') );
        $metadata->setAttributeVisible( 'Phone', true );
        $metadata->setAttributeRequired( 'Language', false );
        $metadata->setAttributeOrderNum('Photo', 1);
        $metadata->addAttributeGroup('Email', 'alternative-key');
        $metadata->addAttributeGroup('Login', 'alternative-key');
        $metadata->setAttributeVisible( 'LDAPUID', true );
        $metadata->setAttributeType( 'LDAPUID', 'varchar' );

        foreach(array('NotificationEmailType', 'NotificationTrackingType', 'SendDeadlinesReport') as $attribute) {
            $metadata->addAttributeGroup($attribute, 'notifications-tab');
        }
        $metadata->setAttributeType('NotificationEmailType', 'REF_NotificationId');
        $metadata->setAttributeType('NotificationTrackingType', 'REF_NotificationTrackingTypeId');
        $metadata->addAttributeGroup('SendDeadlinesReport', 'bulk');

        foreach(array('Phone', 'IsReadonly') as $attribute) {
            $metadata->addAttributeGroup($attribute, 'nonbulk');
        }
    }
}