<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewCommonWebMethod extends FilterWebMethod
 {
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewCustomDictionaryWebMethod extends ViewCommonWebMethod
 {
 	var $object, $attribute;
 	
 	function ViewCustomDictionaryWebMethod( $object = null, $attribute = '' )
 	{
 		$this->object = $object;
 		$this->attribute = $attribute;
 		
 		parent::FilterWebMethod();
 	}
 	
 	function getCaption()
 	{
 		return $this->object->getAttributeUserName( $this->attribute );
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
 		$values = array();
 		$lov = array();
 		
 		$values['all'] = translate('Все');
 		$values[] = '';
 		
 		$attr = $model_factory->getObject('pm_CustomAttribute');
 		
 		$attribute_it = $attr->getByEntity( $this->object );
 		
 		while( !$attribute_it->end() )
 		{
 			if ( $attribute_it->get('ReferenceName') == $this->attribute )
 			{
 				$lov = $attribute_it->toDictionary();
 				break;
 			}
 			
 			$attribute_it->moveNext();
 		}
 		
 		foreach( $lov as $key => $value )
 		{
 			$values[' '.$key] = $value;
 		}
 		
 		$values[] = '';
 		$values['none'] = text(2030);
 		
  		return $values;
	}
	
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 			return 'all'; 
 		}
 		
 		return $value;
 	}
	
 	function getStyle()
	{
		return 'width:125px;';
	}
	
	function getValueParm()
	{
		return $this->attribute;
	}
	
	function getType()
	{
		return 'singlevalue';
	}
	
	function drawSelect()
	{
		parent::drawSelect( 
			array (
				'attribute' => $this->attribute
			)
		);
	}
	
 	function execute_request()
 	{
 		global $_REQUEST;
 		
 		$this->attribute = $_REQUEST['attribute'];
 		
 		parent::execute_request();
 	}
 }

?>