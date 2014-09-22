<?php

class FieldCustomDictionary extends FieldDictionary
{
 	var $object, $attribute_it, $lov;
 	
 	function __construct( $object, $reference_name )
 	{
 		$attr = getFactory()->getObject('pm_CustomAttribute');
 		
 		$this->attribute_it = $attr->getByEntity( $object );
 		
 		while( !$this->attribute_it->end() )
 		{
 			if ( $this->attribute_it->get('ReferenceName') == $reference_name )
 			{
 				$this->lov = $this->attribute_it->toDictionary();
 				
 				break;
 			}
 			
 			$this->attribute_it->moveNext();
 		}

 		parent::__construct($object);
 	}
 	
 	function getOptions()
	{
	    $options = array();
	    
 		foreach( $this->lov as $key => $value )
		{
		    $options[] = array (
                'value' => $key,
                'caption' => translate($value),
                'disabled' => false
            );
		}
		
		return $options;
	}
}