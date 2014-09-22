<?php

class WrtfCKEditorHtmlParser extends WrtfCKEditorPageParser
{
 	function parse( $content = null )
	{
	    $content = parent::parse($content);
	    
	    $content = preg_replace('/<br[^>]*>/i', '', $content);
	    $content = preg_replace('/<p>(\xA0|\s|\&nbsp;)*<\/p>/i', '', $content);
	    
	    return $content;
	}
}
