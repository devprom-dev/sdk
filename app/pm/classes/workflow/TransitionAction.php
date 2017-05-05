<?php
include "TransitionActionIterator.php";

class TransitionAction extends Metaobject
{
 	function __construct()
 	{
 		parent::__construct('pm_TransitionAction');
 		
 		$this->setAttributeCaption( 'ReferenceName', translate('Действие') );
 		$this->setAttributeType( 'ReferenceName', 'REF_StateBusinessActionId' );
 	}
 	
 	function createIterator() {
 		return new TransitionActionIterator( $this );
 	}
}
