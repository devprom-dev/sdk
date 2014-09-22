<?php

class CustomDictionaryParser extends ObjectReferenceParser
{
 	function parse( $reference_name, $attribute_type )
 	{
 		global $model_factory;

 		switch ( $attribute_type )
 		{
 			case 'REF_pm_CustomDictionaryId':
 				
 				$object = new PMCustomDictionary();
 				
 				$object->addFilter( new FilterPredicate(get_class($this->getObject()).",".$reference_name) );
 				
 				return $object;
 				
 			default:
 				return null;
 		}
 	}
}
