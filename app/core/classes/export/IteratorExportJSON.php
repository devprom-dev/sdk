<?php

include_once "IteratorExport.php";

class IteratorExportJSON extends IteratorExport
{
     function __construct( $iterator )
     {
         global $model_factory;

         $log = $model_factory->getObject('ObjectChangeLog');
         
         $log->addFilter( new ChangeLogObjectFilter(get_class($iterator->object)) );
         
         $log->addFilter( new FilterModifiedAfterPredicate(
                 date('Y-m-d H:i:s', strtotime('-1 day', strtotime(date('Y-m-j')))) )
         );
         
         $log->addSort( new SortRecentClause() );
         
         $ids = $iterator->idsToArray();
         
         $result_data = array();
         
         $it = $log->getAll();

         while ( !$it->end() )
         {
             $process = $it->get('ChangeKind') == 'deleted' || in_array($it->get('ObjectId'), $ids);
         
             if ( $process )
             {
                 $result_data[] = array(
                         $iterator->object->getClassName().'Id' => $it->get('ObjectId'),
                         'RecordModified' => $it->get_native('RecordModified')
                 );
             }
         
             $it->moveNext();
         }

         return parent::IteratorExport($iterator->object->createCachedIterator($result_data));         
     }
     
	function export()
	{
 		$uid = new ObjectUID;

	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");
		header('Content-Type: application/json; charset=utf-8');

 		$fields = $this->getFields();
 		
 		$fields['ID'] = 'ID';
 		 
 		$objects = array();
 		
 		$iterator = $this->getIterator();
 		
 		while( !$iterator->end() )
 		{
 			$values = array();
 			
 			foreach( $fields as $key => $field )
 			{
 				switch ( $key )
 				{
 					case 'ID':
 					    
 						$text = $iterator->getId();
 						
 						break;
 					
 					case 'UID':
 						
 					    $text = $uid->getObjectUid($iterator);
 						
 					    break;

 					case 'RecordCreated':
 					case 'RecordModified':
 						
 					    $text = $iterator->get_native($key);
 						
 					    break;
 						
 					default:
 						
 					    $value = $iterator->get($key);
 					    
 					    if ( is_array($value) ) $value = join(',', $value);
 					    
 					    $text = html_entity_decode(IteratorBase::wintoutf8($value), ENT_COMPAT | ENT_HTML401, 'cp1251'); 
 				}
 				
 				$text = str_replace(chr(10), '', str_replace(chr(13), '', $text));
 				
 				$text = str_replace('"', '\"', $text);
 				
 				array_push( $values, '"'.$key.'":"'.$text.'"' ); 
 			}
 			array_push( $objects, "{".join($values, ',')."}" );
 			
 			$iterator->moveNext();
 		}

 		echo '['.join($objects, ',').']';
 	}
}
 