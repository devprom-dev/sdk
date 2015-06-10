<?php

require_once (SERVER_ROOT_PATH.'ext/xml/xml2Array.php');

class IteratorXml extends IteratorBase
{
 	var $path, $pos, $records;
 	
 	function IteratorXml ( $object, $xml )
 	{
 		global $model_factory;
 		
 		parent::IteratorBase( $object );
 		
		$xml_array = new xml2Array;
		$xml_data = $xml_array->xmlParse($xml);

		$entity = $xml_data;
		if ( strtolower($xml_data['name']) != 'entities' )
		{
			$data[0] = $xml_data;
		}
		else
		{
			$data = $xml_data['children'];
		}

		$this->records = array();
		
		foreach ( $data as $entity )
		{
			if ( strtolower(get_class($object)) == 'metaobject' )
			{
				$matched_class = $entity['attrs']['CLASS'] == $object->getEntityRefName();
			}
			else
			{
			    $class_name = $model_factory->getClass($entity['attrs']['CLASS']);
			    
			    if ( $class_name == '' || !class_exists($class_name, false ) ) continue;
			    
				$tmp = $model_factory->getObject($class_name);
				
				$matched_class = is_a($tmp, get_class($object));
			}
				
			if ( !$matched_class ) continue;
			
			if ( !is_array($entity['children']) ) continue;
			
			foreach ( $entity['children'] as $object_tag )
			{
				$record[$object->getEntityRefName().'Id'] = $object_tag['attrs']['ID'];

				foreach ( $object_tag['children'] as $attr_tag )
				{
					if ( $attr_tag['attrs']['ENCODING'] != '' ) {
							$attr_tag['tagData'] = base64_decode($attr_tag['tagData']);
					}
					if ( in_array($entity['attrs']['ENCODING'], array('','windows-1251')) ) {
							$attr_tag['tagData'] = $this->wintoutf8($attr_tag['tagData']);
					}
					$record[$attr_tag['attrs']['NAME']] = $attr_tag['tagData'];
				}
				
				array_push( $this->records, $record );
			}
		}

		$this->setData( $this->records[0] );
 	}
 	
 	function count() 
 	{
 		return count($this->records);
 	}
 	
 	function moveFirst() 
 	{
 		$this->pos = 0;
		$this->setData( $this->records[$this->pos] );
 	}
 	
 	function moveToPos( $offset ) 
 	{
 		$this->pos = $offset;
		$this->setData( $this->records[$this->pos] );
 	}
 	
 	function moveNext() 
 	{
 		$this->pos++;
		$this->setData( $this->records[$this->pos] );
 	}
 	
 	function setData( $data )
 	{
 	    global $_FILES;
 	    
 	    parent::setData( $data );
 	    
 	    if ( !is_array($data) ) return;
 	    
 	    foreach ( $data as $key => $value )
 	    {
 	        if ( $this->object->getAttributeDbType($key) != 'FILE' ) continue;

 	        if ( $value == '' ) continue;
 	        
	        $filename = tempnam(SERVER_FILES_PATH, 'tux');
		    
	        file_put_contents($filename, $value);

	        $info = pathinfo($data[$key.'Ext']);
	        
		 	$_FILES[$key]['tmp_name'] = $filename;
		 	$_FILES[$key]['name'] = $data[$key.'Ext'];
		 	$_FILES[$key]['type'] = $info['extension'];
 	    }
 	}

	static function wintoutf8($s)
 	{
 		if ( function_exists('mb_convert_encoding') ) return mb_convert_encoding($s, "utf-8", "cp1251");
 		if ( function_exists('iconv') ) return iconv("cp1251", "utf-8//IGNORE", $s);
		  $t = '';
		  for ($i = 0, $m = strlen($s); $i < $m; $i++) {
		    $c = ord($s[$i]);
		    if ($c <= 127) { $t .= chr($c); continue; }
		    if ($c >= 192 && $c <= 207) { $t .= chr(208) . chr($c - 48); continue; }
		    if ($c >= 208 && $c <= 239) { $t .= chr(208) . chr($c - 48); continue; }
		    if ($c >= 240 && $c <= 255) { $t .= chr(209) . chr($c - 112); continue; }
		    if ($c == 184) { $t .= chr(209) . chr(209); continue; };
		    if ($c == 168) { $t .= chr(208) . chr(129); continue; };
		  }
		  return $t;
 	}   
}