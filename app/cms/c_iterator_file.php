<?php

 class IteratorFile extends IteratorBase
 {
 	var $path, $pos;
 	
 	function IteratorFile ( $object, $path, $ext = '*' )
 	{
 		parent::IteratorBase( $object );
 		
 		$files = array();
 		
 		if ( !is_dir($path) ) return;
 		
		$mydir = dir($path);
		
		if ( !is_object($mydir) ) return;
		
   		while( ($file = $mydir->read()) !== false ) 
   		{
   			if ( $file == '.' || $file == '..' ) continue;
   			
   			if ( !is_dir($path.'/'.$file) ) 
   			{
   				$info = pathinfo($path.'/'.$file);
   				if ( $ext != '*' && strtolower($info['extension']) != strtolower($ext) )
   				{
   					continue;
   				}
   				
   				$stat = stat($path.'/'.$file);
   				
   				$db_format = str_replace('%', '', getSession()->getLanguage()->getDateFormat().' %H:%i');

   				$createddate = date($db_format, $stat['ctime']);

   				array_push( $files,  array( 
   				    'created' => $createddate, 
   				    'RecordCreated' => $createddate,
   				    'name' => $file, 
   				    'Caption' => $file, 
   				    'size' => $stat['size'], 
					'ctime' => $stat['ctime'],
   				    is_a($object, 'Metaobject') ? $object->getClassName().'Id' : 'id' => abs(crc32($file)) 
   				));
   			}
   		}

   		$mydir->close();
   		
   		arsort($files);
   		
   		$this->setRowset( $files );
 	}
 	
 	function sortCreated()
 	{
 	    $rowset = $this->getRowset();
 	    
		usort( $rowset, "iterator_file_c_sort" );
		
		$this->setRowset( $rowset );
 	}
 	
 	function sortCreatedDesc()
 	{
 	    $rowset = $this->getRowset();
 	    
		usort( $rowset, "iterator_file_rc_sort" );
		
		$this->setRowset( $rowset );
 	}
 }

 function iterator_file_c_sort( $left, $right )
 {
 	return intval($left['ctime']) > intval($right['ctime']) ? 1 : -1;
 }
 
 function iterator_file_rc_sort( $left, $right )
 {
 	return intval($left['ctime']) < intval($right['ctime']) ? 1 : -1;
 }
 
?>