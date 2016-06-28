<?php

include_once SERVER_ROOT_PATH."pm/classes/wiki/validators/ModelValidatorParentPage.php";
include_once "FieldHierarchySelector.php";

class FieldHierarchySelectorAppendable extends FieldHierarchySelector
{
    function draw( $view = null )
    {
    	$this->setAppendable();
    	parent::draw();
    }
    
    function getValidator()
    {
    	return new ModelValidatorParentPage();
    }
}