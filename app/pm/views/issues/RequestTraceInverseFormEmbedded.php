<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";

class RequestTraceInverseFormEmbedded extends ObjectTraceFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ChangeRequest':
 				return true;
 			
 			default:
 				return false;
 		}
 	}
 	
 	public function showDeliveryDate( $show ) {
 		$this->show_delivery_date = $show;
 	}
 	
 	function drawFieldTitle( $attr )
 	{
 	}
 	
 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'ChangeRequest':
 			    $object = $this->getObject()->getAttributeObject($attr);
				$field = new FieldAutoCompleteObject($object);
				$field->setTitle( $object->getDisplayName() );
				$field->setCrossProject();
				return $field;
				
 			default:
 				return parent::createField( $attr );
 		}
 	}
 	
  	function getTargetIt( $object_it )
 	{
 	    return $object_it->getRef('ChangeRequest');
 	}
 	
 	function getItemDisplayName( $object_it )
 	{
 		$title = parent::getItemDisplayName($object_it);
 		
 		if ( $this->show_delivery_date && $object_it->get('DeliveryDate') != '' ) {
 			$title .= str_replace('%1', $object_it->getDateFormatted('DeliveryDate'), text(2031));
 		}
 		
 		return $title;
 	}

	function getListItemsAttribute() {
		return 'ChangeRequest';
	}

	private $show_delivery_date = false;
}