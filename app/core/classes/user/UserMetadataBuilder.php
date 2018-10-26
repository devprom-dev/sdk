<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class UserMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'cms_User' ) return;

        $metadata->addPersister( new UserReadonlyPersister() );

        $system_attributes = array ('IsAdmin', 'IsShared', 'Rating', 'IsActivated', 'SessionHash', 'ICQ', 'Skype', 'LDAPUID', 'AskChangePassword', 'PhotoExt', 'PhotoPath');
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
        $metadata->setAttributeType( 'Phone', 'RICHTEXT' );
        $metadata->setAttributeRequired( 'Language', false );
        $metadata->setAttributeOrderNum('Photo', 1);
        $metadata->addAttributeGroup('Email', 'alternative-key');
        $metadata->addAttributeGroup('Login', 'alternative-key');
    }
}