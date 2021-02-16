<?php
include_once "SpentTimeForm.php";
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
        $object = getFactory()->getObject('Activity');
        if ( !$this->needDisplayForm() ) return $object;

        if ( $_REQUEST[$object->getIdAttribute()] > 0 ) {
            $objectIt = $object->getExact($_REQUEST[$object->getIdAttribute()]);
            if ( $objectIt->get('Issue') > 0 ) {
                $this->anchor_it = $objectIt->getRef('Issue');
                $object = getFactory()->getObject('ActivityRequest');
            }
            else if ($objectIt->get('Task') > 0) {
                $this->anchor_it = $objectIt->getRef('Task');
                $object = getFactory()->getObject('ActivityTask');
            }
            else {
                $object = getFactory()->getObject('ActivityTask');
                $this->anchor_it = getFactory()->getObject('Task')->getEmptyIterator();
            }
            return $object;
        }

        if ( !class_exists($_REQUEST['class']) ) return $object;

		$target = getFactory()->getObject($_REQUEST['class']);
		$this->anchor_it = $target->getExact($_REQUEST['object']);

		return getFactory()->getObject(
		    $target->getEntityRefName() == 'pm_ChangeRequest' ? 'ActivityRequest' : 'ActivityTask'
        );
	}

 	function getEntityForm()
 	{
 		$form = new SpentTimeForm( $this->getFormObject() );
 		$form->setAnchorIt($this->anchor_it);
 		return $form;
 	}
 	
	private $anchor_it;
}
