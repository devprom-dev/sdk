<?php
include_once "SpentTimeForm.php";
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include "SpentTimeTable.php";

class SpentTimePage extends PMPage
{
    function getObject() {
        return getFactory()->getObject('ActivityTask');
    }

    function getTable() {
        return new SpentTimeTable($this->getObject());
    }

    function getFormObject()
	{
		$class_name = in_array(strtolower($_REQUEST['class']), array('request', 'pm_changerequest')) ? 'Request' : 'Task';
		$target = getFactory()->getObject($class_name);
		$this->anchor_it = $target->getExact($_REQUEST['object']);

		return getFactory()->getObject(
		    is_a($target, 'Request') ? 'ActivityRequest' : 'ActivityTask'
        );
	}

 	function getForm() 
 	{
 		$form = new SpentTimeForm( $this->getFormObject() );
 		$form->setAnchorIt($this->anchor_it);
 		return $form;
 	}
 	
	private $anchor_it;
}
