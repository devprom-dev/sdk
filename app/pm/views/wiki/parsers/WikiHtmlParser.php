<?php

include_once "WikiParser.php";

class WikiHtmlParser extends WikiParser
{
	function __construct ( $wiki_it )
 	{
 		parent::__construct( $wiki_it );
 	}

	function hasUrlOnImage()
	{
		return false;
	}
}
