<?php
include "MentionedIterator.php";
include "MentionedRegistry.php";

class Mentioned extends Metaobject
{
    private $attributesObject = null;

 	function __construct() {
 		parent::Metaobject('entity', new MentionedRegistry($this));
 	}
 	
 	function createIterator() {
 		return new MentionedIterator( $this );
 	}

    function getVpds() {
        return getFactory()->getObject('ProjectRole')->getVpds();
    }

    function setAttributesObject( $object ) {
         $this->attributesObject = $object;
    }

    function getAttributesObject() {
         return $this->attributesObject;
    }
}