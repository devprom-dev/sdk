<?php

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
 		if ( !file_exists($filepath) ) {
 			exit(header("HTTP/1.0 404 Not Found"));
 		}

		$file_time = gmdate( "D, d M Y H:i:s T", filemtime($filepath) );
		$etagFile = md5($file_time);

		$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
		if ($etagHeader == $etagFile) {
			exit(header("HTTP/1.1 304 Not Modified"));
		}

		$file = fopen( $filepath, "rb" );
		if ( $file === false ) {
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
		if ( $filename == '' ) {
			$filename = basename($filepath);
		}

		header('Cache-Control: public');
		header("ETag: ". $etagFile);
		header("Last-Modified: " . $file_time); // always modified

		if ( $this->attachment ) {
			header(EnvironmentSettings::getDownloadHeader($filename));
		}
		 
		header("Content-Length: ".($file_size - $offset)); 
		header("Content-Type: ".$mime."; charset=".APP_ENCODING); 
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
