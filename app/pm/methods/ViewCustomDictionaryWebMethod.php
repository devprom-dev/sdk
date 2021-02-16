<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewCustomDictionaryWebMethod extends FilterWebMethod
{
 	var $object, $attribute;
 	
 	function __construct( $object = null, $attribute = '' )
 	{
 		$this->object = $object;
 		$this->attribute = $attribute;
 		parent::__construct();
 	}
 	
 	function getCaption() {
 		return $this->object->getAttributeUserName( $this->attribute );
 	}

 	function getValues()
 	{
 		$values = array();
 		$lov = array();

 		$values['all'] = translate('Все');
        $values['none'] = translate('<нет значения>');

 		$attribute_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->object );
 		while( !$attribute_it->end() )
 		{
 			if ( $attribute_it->get('ReferenceName') == $this->attribute )
 			{
 				$lov = $attribute_it->toDictionary();
 				break;
 			}
 			
 			$attribute_it->moveNext();
 		}
 		
 		foreach( $lov as $key => $value ) {
 			$values[' '.$key] = $value;
 		}

  		return $values;
	}
	
 	function getStyle()
	{
		return 'width:125px;';
	}
	
	function getValueParm()
	{
		return $this->attribute;
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