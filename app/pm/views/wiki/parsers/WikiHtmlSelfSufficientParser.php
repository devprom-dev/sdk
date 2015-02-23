<?php

include_once "WikiParser.php";

class WikiHtmlSelfSufficientParser extends WikiParser
{
	private $href_resolver_func = null;
	
 	function __construct ( $wiki_it )
 	{
 		parent::__construct( $wiki_it );
 		$this->href_resolver_func = function($it) {};
 	}
 	
 	function setHrefResolver( $func )
 	{
 		$this->href_resolver_func = $func; 
 	}

	function hasUrlOnImage()
	{
		return false;
	}
	
	function getPageUrl( $wiki_it = null ) 
	{
		return call_user_func($this->href_resolver_func, $wiki_it);
	}
	
	function replaceImageCallback( $match )
	{
		$text_array = array();
		if ( preg_match('/^(.*)\s+(text|width)=(.*)$/i', $match[1], $text_array) > 0 ) {
			$image_name = $text_array[1];
		}
		else {
			$image_name = $match[1];
		}
	
		$image_it = $this->getFileByName($image_name);
		if ( $image_it->getId() < 1 ) return parent::replaceImageCallback( $match ); 
		
     	$image = file_get_contents($image_it->getFilePath('Content'));
     	if ( $image === false ) return $match[0];
     	
     	if ( $text_array[2] == '' ) $text_array[2] = 'alt';
     	return '<img src="data:image;base64,'.base64_encode($image).'" '.$text_array[2].'="'.$text_array[3].'">';
	}
}
