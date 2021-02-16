<?php
include_once "FieldHierarchySelector.php";

class FieldHierarchySelectorAppendable extends FieldHierarchySelector
{
    function draw( $view = null )
    {
    	$this->setAppendable();
    	parent::draw();
    }
}