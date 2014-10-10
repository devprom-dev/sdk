<?php

class CustomDictionaryParser extends ObjectReferenceParser
{
 	function parse( $reference_name, $attribute_type )
 	{
 		switch ( $attribute_type )
 		{
 			case 'REF_pm_CustomDictionaryId':
 				return new PMCustomDictionary($this->getObject(), $reference_name);
 				
 			default:
 				return null;
 		}
 	}
}
