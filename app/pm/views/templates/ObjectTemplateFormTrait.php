<?php

trait ObjectTemplateFormTrait
{
    private $templateObject = null;

    public function traitConstruct($templateObject)
    {
        $this->templateObject = $templateObject;
        $templateId = $_REQUEST[$this->templateObject->getIdAttribute()];
        if ( $templateId > 0 ) {
            $this->setTemplate($templateId);
        }
        $templateAction = $_REQUEST[$this->templateObject->getEntityRefName() . 'action'];
        if ( $templateAction != '' ) {
            $this->setAction($templateAction);
        }
    }

    public function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        $requestIt = $object->getExact($_REQUEST['items']);

        $templatedAttributes = array_merge(
            $this->templateObject->getAttributesTemplated(),
            array( 'Project' )
        );
        foreach( $object->getAttributes() as $attribute => $info ) {
            if ( !in_array($attribute, $templatedAttributes) ) {
                $object->setAttributeVisible($attribute, false);
            }
            if ( !array_key_exists($attribute, $_REQUEST) ) {
                $_REQUEST[$attribute] = $requestIt->getHtmlDecoded($attribute);
            }
            $object->setAttributeRequired($attribute, false);
        }

        $idAttribute = $this->templateObject->getIdAttribute();
        if ( $_REQUEST[$idAttribute] > 0 ) {
            $object->addAttribute($idAttribute, 'INTEGER', $idAttribute, false, false);
            $object->setAttributeDefault($idAttribute, $_REQUEST[$idAttribute]);
            $object->setAttributeRequired($idAttribute, true);
        }

        $object->addAttribute('Recurring', 'REF_pm_RecurringId', translate('Повторять'), true);
        $object->addAttributeGroup('Recurring', 'additional');
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'entity' => $this->templateObject->getEntityRefName()
            )
        );
    }

    function process()
    {
        getFactory()->transformEntityData($this->getObject(), $_REQUEST);

        $objectIt = $this->getObject()->createCachedIterator(
            array(
                array_merge(
                    $_REQUEST,
                    array (
                        $this->getObject()->getIdAttribute() => '1'
                    )
                )
            )
        );
        $this->setObjectIt($objectIt);

        return parent::process();
    }

    function persist()
    {
        $objectIt = $this->getObjectIt();
        $id = $_REQUEST[$this->templateObject->getIdAttribute()];

        if ( $this->getAction() == 'add') {
            $id = $this->templateObject->add_parms(
                array(
                    'Caption' => $_REQUEST['Caption'],
                    'Recurring' => $_REQUEST['Recurring'],
                    'ListName' => $this->templateObject->getListName(),
                    'ObjectClass' => get_class($this->getObject())
                )
            );
            $this->templateObject->persistSnapshot($id, $objectIt);
            return true;
        }

        if ( $this->getAction() == 'modify') {
            $this->templateObject->modify_parms( $id,
                array(
                    'Caption' => $_REQUEST['Caption'],
                    'Recurring' => $_REQUEST['Recurring']
                )
            );
            $this->templateObject->persistSnapshot($id, $objectIt);
            return true;
        }

        if ( $this->getAction() == 'delete') {
            $this->templateObject->delete( $id );
            return true;
        }
    }

    function getFieldDescription($field_name)
    {
        switch ($field_name) {
            case 'Recurring':
                $moduleIt = getFactory()->getObject('Module')->getExact('dicts-recurring');
                return sprintf(text(3104), $moduleIt->getUrl());
        }
        return parent::getFieldDescription($field_name);
    }
}