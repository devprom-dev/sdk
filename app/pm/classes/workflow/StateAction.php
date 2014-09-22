<?php

include "StateActionIterator.php";

class StateAction extends Metaobject
{
 	function StateAction() 
 	{
 		parent::Metaobject('pm_StateAction');
 		
 		$this->setAttributeCaption( 'ReferenceName', translate('Действие') );
 		
 		$this->setAttributeType( 'ReferenceName', 'REF_StateBusinessActionId' );
 	}
 	
 	function createIterator() 
 	{
 		return new StateActionIterator( $this );
 	}
}
