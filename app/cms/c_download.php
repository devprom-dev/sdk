<?php

 //////////////////////////////////////////////////////////////////////////
 class Downloader
 {
 	var $attachment;
 	
 	function Downloader()
 	{
 		$this->attachment = true;
 	}
 	
 	function disableAttachment()
 	{
 		$this->attachment = false;
 	}
 	
 	/*
 	 * is used when a file with name $filename is downloaded by user
 	 * supports partital download
 	 */
 	function echoFile( $filepath, $filename = '', $mime = 'application/octet-stream' )
 	{
		global $customerrorhandler, $_SERVER;

		if ( is_object($customerrorhandler) )
		{
			$customerrorhandler->disable();
			ob_end_clean();
		}
 		
 		if ( !file_exists($filepath) )
 		{
 			exit(header("HTTP/1.0 404 Not Found"));
 		}

		$file = fopen( $filepath, "rb" ); 

		if ( $file === false ) 
		{ 
		  	exit(header ("HTTP/1.0 403 Forbidden")); 
		} 

		$offset = 0;
		
		if ( $_SERVER['HTTP_RANGE'] != '' ) 
		{ 
		  	$offset = $_SERVER['HTTP_RANGE']; 
		  	$offset = str_replace("bytes=", "", $offset); 
		  	$offset = str_replace("-", "", $offset); 
		  	
		  	if ( $offset > 0 ) fseek($file, $offset); 
		}

		$file_size = filesize($filepath); 
		$file_time = date( "D, d M Y H:i:s T", filemtime($filepath) ); 

		if ( $filename == '' )
		{
			$filename = basename($filepath);
		}

	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");

		if ( $this->attachment )
		{
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}
		 
		header("Content-Length: ".($file_size - $offset)); 
		header("Content-Type: ".$mime."; charset=windows-1251;"); 
		header("Accept-Ranges: bytes"); 
		header("Content-Range: bytes ".$offset."-".($file_size - 1)."/".$file_size); 

		$read_step = 10000;
		$red = 0;

		while ( $red < $file_size ) 
		{
			echo fread( $file, $read_step );
			$red += $read_step;
		}

		fclose($file); 
 	}
 }

?>