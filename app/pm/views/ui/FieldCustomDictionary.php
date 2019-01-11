<?php

class FieldCustomDictionary extends FieldDictionary
{
 	var $object, $attribute_it, $lov;
 	
 	function __construct( $object, $reference_name )
 	{
 	    if ( $object instanceof IteratorBase ) {
            $objectIt = $object;
            $object = $object->object;
        }

        parent::__construct($object);

 	    $attribute = getFactory()->getObject('pm_CustomAttribute');
        $this->attribute_it = $attribute->getByEntity( $object );

 	    if ( is_object($objectIt) ) {
            $this->attribute_it = $attribute->createCachedIterator(
                array_merge(
                    $this->attribute_it->getRowset(),
                    $attribute->getRegistry()->Query(
                            array(
                                new CustomAttributeObjectPredicate($objectIt)
                            )
                        )->getRowset()
                )
            );
 	    }

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