<?php

class PageSectionAttributes extends InfoSection
{
    private $referenceName = array();
    private $object = null;
    private $title = '';

    function __construct( $object, $referenceName, $title )
    {
        $this->referenceName = !is_array($referenceName) ? array($referenceName) : $referenceName;
        $this->setObject($object);
        $this->title = $title;
        parent::__construct();
    }

    function getCaption() {
        return $this->title;
    }

    function setObject( $object ) {
        $this->object = $object;
    }

    function getId() {
        return join('-',$this->referenceName);
    }

    function getAttributes() {
        $attributes = array();
        foreach( $this->referenceName as $referenceName ) {
            foreach( $this->object->getAttributesByGroup($referenceName) as $attribute ) {
                if ( $this->object->IsAttributeRequired($attribute) ) continue;
                $attributes[] = $attribute;
            };
        }
        return $attributes;
    }

    function getFields() {
        $attributes = array();
        foreach( $this->referenceName as $referenceName ) {
            foreach( $this->object->getAttributesByGroup($referenceName) as $attribute ) {
                if ( $this->object->IsAttributeRequired($attribute) ) continue;
                $attributes[] = $this->object->getEntityRefName().$attribute;
            };
        }
        return $attributes;
    }
}
