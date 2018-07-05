<?php

 if ( preg_match('/^[a-zA-Z0-9\_]+$/im', $_REQUEST['class']) < 1 )
 {
 	unset($_REQUEST['class']);
 }

 if ( preg_match('/^[a-zA-Z0-9\_]+$/im', $_REQUEST['entity']) < 1 )
 {
 	unset($_REQUEST['entity']);
 }

 if ( $_REQUEST['entity'] != '' ) 
 {
 	$object = new Metaobject($_REQUEST['entity']);	
 } 
 else if ( $_REQUEST['class'] != '' ) 
 {
     $className = getFactory()->getClass($_REQUEST['class']);
     if ( !class_exists($className) ) {
         exit(header('Location: /404'));
     }
     $object = getFactory()->getObject($className);
 }
 else
 {
 	exit(header('Location: /404'));
 }

 $attrs = array_keys($object->getAttributes());

 foreach ( $attrs as $attr )
 {
	$type = $object->getAttributeType($attr);

	if ( strtolower($type) == 'file' || strtolower($type) == 'image' )
	{
		 $it = $object->getExact( array_shift(preg_split('/\./',$_REQUEST['id'])) );
		 $file_name = SERVER_FILES_PATH.$object->getClassName().'/'.basename($it->getFilePath( $attr ));
		
		 if ( file_exists($file_name) )
		 {
			 $downloader = new Downloader;

			 $downloader->echoFile( $file_name, $it->getFileName( $attr ), 
			 	$it->getFileMime( $attr ));
			 	
			 die();
		 }
	}
 }

 exit(header('Location: /404'));
