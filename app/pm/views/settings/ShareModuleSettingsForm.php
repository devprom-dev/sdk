<?php
use Devprom\ProjectBundle\Service\Project\ShareWidgetService;

class ShareModuleSettingsForm extends PMPageForm
{
    function extendModel()
    {
        $object = $this->getObject();

        foreach( array_keys($object->getAttributes()) as $attribute ) {
            $object->setAttributeVisible($attribute, false);
            $object->setAttributeRequired($attribute, false);
        }
        $object->addAttribute('URL', 'VARCHAR', translate('Ссылка'), true, false, text(2604));
        $object->setAttributeRequired('URL', true);

        $object->addAttribute('Email', 'email', 'Email', true, true, text(2606));
        $object->setAttributeRequired('Email', true);

        $object->addAttribute('Subject', 'VARCHAR', text(2607), true, false, text(2608));
        $object->setAttributeRequired('Subject', true);

        if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ) {
            $object->addAttribute('ProjectRole', 'REF_ProjectRoleInheritedId', text(2610), true, false, text(2611));
            $object->setAttributeRequired('ProjectRole', true);
        }
    }

    function getFieldValue($field)
    {
        switch( $field ) {
            case 'Subject':
                return text(2609);
            default:
                return parent::getFieldValue($field);
        }
    }

    function createFieldObject($attr)
    {
        switch( $attr ) {
            case 'URL':
                return new FieldCopyClipboard();
            case 'Email':
                return new FieldEmail();
            default:
                return parent::createFieldObject($attr);
        }
    }

    function persist()
    {
        if ( $_REQUEST['Email'] != '' && $_REQUEST['URL'] != '' ) {
            $service = new ShareWidgetService(getFactory(), getSession());
            $service->execute(
                $_REQUEST['Email'],
                $_REQUEST['Subject'],
                $_REQUEST['URL'],
                getFactory()->getObject('ProjectRole')->getExact($_REQUEST['ProjectRole']),
                getSession()->getProjectIt()
            );
        }

        return true;
    }
}