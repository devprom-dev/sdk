<?php
include "FieldIssueTrace.php";
include "RequestTraceInverseFormEmbedded.php";
 
class FieldIssueInverseTrace extends FieldIssueTrace
{
 	function getFilters()
 	{
		return array ( 
				new RequestTraceObjectPredicate(is_object($this->getObjectIt()) ? $this->getObjectIt() : 0) 
		);  
 	}
 	
 	public function showDeliveryDate( $show = true ) {
 		$this->show_delivery_date = $show;
 	}
 	
 	function getForm( & $trace )
	{
		$form = new RequestTraceInverseFormEmbedded( $trace, 'ObjectId', $this->getName() );
		$form->showDeliveryDate( $this->show_delivery_date );
		return $form;
	}

	private $show_delivery_date = false;
}
