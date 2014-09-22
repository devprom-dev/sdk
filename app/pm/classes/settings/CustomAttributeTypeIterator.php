<?php

class CustomAttributeTypeIterator extends OrderedIterator
{
 	function getDbType()
 	{
 		$type = $this->get('ReferenceName');
 		
 		switch ( strtolower($type) )
 		{
 			case 'dictionary':
 				return 'REF_pm_CustomDictionaryId';
 				
 			case 'string':
 				return 'varchar';
 				
 			default:
 				return $type;
 		}
 	}
 	
 	function getValueColumn()
 	{
 		$type = $this->get('ReferenceName');
 		
 		switch ( strtolower($type) )
 		{
 			case 'integer':
 			case 'dictionary':
 			case 'reference':
 				$value_column = 'IntegerValue';
 				break;

 			case 'string':
 			case 'date':
 				$value_column = 'StringValue';
 				break;

 			case 'password':
 				$value_column = 'PasswordValue';
 				break;
 				
 			case 'text':
 			case 'wysiwyg':
 				$value_column = 'TextValue';
 				break;
 				
 			default:
 				$value_column = 'IntegerValue';
 				break;
 		}
 		
 		return $value_column;
 	}
}