<?php

include_once "WebMethod.php";

class AutocompleteWebMethod extends WebMethod
{
 	var $method_name, $object, $title;
 	
 	function AutocompleteWebMethod ( $object = null, $title = '' )
 	{
 		$this->method_name = md5(get_class($object).$this->getMethodName());
 		$this->object = $object;
		
		$this->title = $title == '' ? ( is_object($this->object) ? $this->object->getDisplayName() : '') : $title;
 		
 		parent::WebMethod();
 	}

 	function hasAccess()
 	{
 		return true;
 	}

	function getMethodName()
	{
		return $this->method_name;
	}
	
	function getTitle()
	{
		return $this->title;
	}
	
	function getCaption()
	{
		return $this->getTitle();
	}
	
 	function exportHeaders()
 	{
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header('Content-type: text/html; charset=utf-8');
 	}
	
 	function getResult( $object_it, $attributes )
 	{
		$object_uid = new ObjectUid;
 	    
		$result = array();
		
 	    while( !$object_it->end() )
	    {
	    	$caption = $object_uid->getUidTitle($object_it);
	    	
	        if ( $object_uid->hasUid($object_it) )
 			{
 			    $info = $object_uid->getUIDInfo($object_it);
 			    
 			    $completed = $info['completed'];
 			}
 			else
 			{
 				$completed = false;
 			}
	    	
 			 $result_item = array (
 			    'id' => html_entity_decode(IteratorBase::wintoutf8($object_it->getId()), ENT_COMPAT | ENT_HTML401, 'utf-8'),
 			    'label' => html_entity_decode(IteratorBase::wintoutf8($caption), ENT_COMPAT | ENT_HTML401, 'utf-8'),
 			    'completed' => $completed
 			);
 			
 			foreach( preg_split('/,/', $attributes) as $attribute )
 			{
 				if ( $object_it->get($attribute) == '' && $object_it->object->getAttributeType($attribute) == '' ) continue;
 				
 				$result_item[$attribute] = $object_it->get($attribute);
 			}
 			
 			$result[] = $result_item;
	        
	        $object_it->moveNext();
	    }
	    
	    return $result;
 	}
 	
 	function execute_request()
 	{
 		global $_REQUEST, $model_factory;

		$object_uid = new ObjectUid;
		
		$attributes = preg_split('/,/', $_REQUEST['attributes']);
		
		if ( $_REQUEST['attributes'] == '' || count($attributes) < 1 ) $attributes = array('Caption');

 		$object = $model_factory->getObject($_REQUEST['class']);
 		
 		if ( is_a($object, 'MetaobjectStatable') )
 		{
 		    $object->addSort( new SortAttributeClause('State') );
 		}
 		
 		$key = 'term';
     	
 		$result = array();
 		
     	$_REQUEST[$key] = trim(IteratorBase::utf8towin($_REQUEST[$key])); 
     	
 		if ( $_REQUEST[$key] == '' )
 		{
 		    $record_count = $object->getRecordCount();
 		    
 		    $result = $this->getResult($record_count < 60 ? $object->getAll() : $object->getFirst(60), $_REQUEST['additional']); 
 		}
 		else
 		{
     		$object_it = $object->getAll();
     		
     		$data = array();
     		
     		while ( !$object_it->end() )
     		{
     			$caption = $object_uid->getUidTitle($object_it);
     			     			
     			foreach( $attributes as $attribute )
     			{
     				$value = $attribute == 'Caption' ? $caption : $object_it->get( $attribute );

     				if ( mb_stripos( trim($value), $_REQUEST[$key] ) !== false )
     				{
     					$data[] = $object_it->getData();
     					
     					break;
     				}
     			}
    
     			$object_it->moveNext();
     		}
     		
     		$result = $this->getResult( $object_it->object->createCachedIterator($data), $_REQUEST['additional'] );
 		}

 		echo JsonWrapper::encode($result);
 	}
}
