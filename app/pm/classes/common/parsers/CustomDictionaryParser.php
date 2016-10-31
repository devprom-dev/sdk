<?php

class CustomDictionaryParser extends ObjectReferenceParser
{
 	function parse( $reference_name, $attribute_type )
 	{
 		switch ( $attribute_type )
 		{
 			case 'REF_PMCustomDictionaryId':
 				return new PMCustomDictionary($this->getObject(), $reference_name);
 				
 			default:
 				return null;
 		}
 	}
}
