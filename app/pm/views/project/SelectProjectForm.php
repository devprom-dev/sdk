<?php

class SelectProjectForm extends PMPageForm
{
    function extendModel()
    {
        $object = $this->getObject();

        foreach( array_keys($object->getAttributes()) as $attribute ) {
            $object->setAttributeVisible($attribute, false);
            $object->setAttributeRequired($attribute, false);
        }

        $object->addAttribute('Project', 'REF_ProjectAccessibleActiveId', translate('Проект'), true, false);
        $object->setAttributeRequired('Project', true);
    }

    function process()
    {
        if ( $_REQUEST['Project'] == '' ) return false;
        echo json_encode(
            array(
                'Id' => getFactory()->getObject('Project')
                            ->getExact($_REQUEST['Project'])->get('CodeName')
            )
        );
        return true;
    }

    function getCaption() {
        return text(2904);
    }
}