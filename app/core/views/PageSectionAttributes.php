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

    function getReferenceName() {
        return $this->referenceName;
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

    function getAttributes( $skipAttributes )
    {
        $attributes = array();
        foreach( $this->referenceName as $referenceName ) {
            $groupAttributes = array_diff(
                $this->object->getAttributesByGroup($referenceName),
                $this->object->getAttributesByGroup('tab-main')
            );
            foreach( $groupAttributes as $attribute ) {
                if ( in_array($attribute, $skipAttributes) ) continue;
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }
}
