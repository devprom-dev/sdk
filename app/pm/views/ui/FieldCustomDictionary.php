<?php

class FieldCustomDictionary extends FieldDictionary
{
 	var $object, $attribute_it, $lov;
 	
 	function __construct( $object, $reference_name )
 	{
		parent::__construct($object);

 		$attr = getFactory()->getObject('pm_CustomAttribute');
 		
 		$this->attribute_it = $attr->getByEntity( $object );
 		while( !$this->attribute_it->end() )
 		{
 			if ( $this->attribute_it->get('ReferenceName') == $reference_name ) {
 				$this->lov = $this->attribute_it->toDictionary();
				if ( $object->IsAttributeRequired($reference_name) && $this->attribute_it->get('DefaultValue') != '' ) {
					$this->setNullOption(false);
				}
 				break;
 			}
 			$this->attribute_it->moveNext();
 		}
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