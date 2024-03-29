<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorEmbeddedForm.php";
include "SpentTimeFormEmbedded.php";
include "SpentTimeFormEmbeddedShort.php";

class FieldSpentTime extends FieldForm
{
 	var $object_it;
 	var $short_form = false;
 	private $showTotal = false;

 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 	}

 	function setShortMode( $short = true ) {
 		$this->short_form = $short;
 	}

 	function setTotalVisible( $value = true ) {
 	    $this->showTotal = $value;
    }
 	
 	function getObject() {
 		return getFactory()->getObject('pm_Activity');
 	}
 	
 	function getObjectIt() {
 		return $this->object_it;
 	}
 	
 	function getAnchorField() {
 		return 'Task';
 	}

	function & getForm( & $activity ) {
        return new SpentTimeFormEmbeddedShort(
            $activity, $this->getAnchorField(), $this->getName() );
	}
	
	function render( $view ) {
	    $this->drawBody( $view );    
	}
	
	function drawBody( $view = null )
	{
		$activity = $this->getObject();
		$activity->addSort( new SortAttributeClause('ReportDate') );
		
 		$object_it = $this->getObjectIt();
 		$activity->setVpdContext($object_it);
		
 		$form = $this->getForm( $activity );
 		$form->showAutoTimeButtons( !$this->short_form );

 		if ( is_object($object_it) ) {
 			$form->setAnchorIt($object_it);
 			if ( !$this->getEditMode() ) $form->setObjectIt( $object_it );
 		}

        if ( $this->showTotal && is_object($object_it) && $object_it->get('Fact') > 0 ) {
            echo '<span title="'.$object_it->object->getAttributeDescription('Fact').'">'.
                translate('Всего').': '.
                    getSession()->getLanguage()->getHoursWording($object_it->get('Fact')).
                '</span>';
        }

        $form->setReadonly( $this->readOnly() );
 		$form->draw( $view );

        if ( is_object($object_it) ) {
            echo '<input type="hidden" name="Fact" value="'.$object_it->get('Fact').'">';
        }
	}
}