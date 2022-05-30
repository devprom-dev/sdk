<?php

class IncludeArtifactsForm extends PMPageForm
{
    private $artifactFields = array();

    function extendModel()
    {
        $object = $this->getObject();
        foreach( $object->getAttributes() as $attribute => $info ) {
            $object->setAttributeVisible($attribute, false);
            $object->setAttributeRequired($attribute, false);
        }

        $pluggableEntities = array(
            'Requirement', 'TestScenario', 'HelpPage'
        );
        $this->artifactFields = array_merge(
            $pluggableEntities,
            array (
                'ProjectPage'
            )
        );
        foreach( $this->artifactFields as $entity ) {
            $className = getFactory()->getClass($entity);
            if ( !class_exists($className) ) continue;

            $object->addAttribute($className, 'REF_'.$className.'Id', getFactory()->getObject($className)->getDisplayName(), true, false);
        }
    }

    function createFieldObject($attr)
    {
        if ( !$this->getObject()->IsReference($attr) ) return null;

        $attributeObject = $this->getObject()->getAttributeObject($attr);
        if ( $attributeObject instanceof WikiPage ) {
            return new FieldHierarchySelector($attributeObject);
        }
        else {
            return null;
        }
    }

    function process()
    {
        if ($this->getAction() != 'add') return false;

        $this->extendModel();
        $uid = new ObjectUID();

        foreach( $this->artifactFields as $attribute ) {
            $value = $this->getFieldValue($attribute);
            if ( $value == '' ) continue;

            $info = $uid->getUidInfo(
                $this->getObject()->getAttributeObject($attribute)->getExact($value),
                true
            );
            echo json_encode(
                array(
                    'Id' => 0,
                    'Url' => '{{'.$info['uid'].'}}'
                )
            );
            return true;
        }
        echo json_encode(
            array(
                'Id' => 0, 'Url' => ''
            )
        );
        return true;
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array (
                'iframe' => true
            )
        );
    }

    function getShortAttributes() {
        return array();
    }

    function getHint() {
        return '';
    }
}