<?php

class CustomAttributeTypeIterator extends CacheableIterator
{
 	function getDbType()
 	{
 		$type = $this->get('ReferenceName');
 		switch ( strtolower($type) )
 		{
 			case 'dictionary':
 				return 'REF_PMCustomDictionaryId';
 			case 'string':
			case 'computed':
 				return 'varchar';
            case 'integer':
                return 'float';
            case 'char':
                return 'char';
 			default:
 				return $type;
 		}
 	}
 	
 	function getValueColumn()
 	{
 		$type = $this->get('ReferenceName');
 		
 		switch ( strtolower($type) )
 		{
 			case 'password':
 				$value_column = 'PasswordValue';
 				break;
 				
 			case 'text':
 			case 'wysiwyg':
 				$value_column = 'TextValue';
 				break;
 				
 			default:
 				$value_column = 'StringValue';
 				break;
 		}
 		
 		return $value_column;
 	}
}