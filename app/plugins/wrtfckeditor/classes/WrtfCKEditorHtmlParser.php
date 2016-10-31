<?php

class WrtfCKEditorHtmlParser extends WrtfCKEditorPageParser
{
 	function parse( $content = null )
	{
	    $content = parent::parse($content);
	    $content = preg_replace('/<p>(\xA0|\s|\&nbsp;)*<\/p>/i', '', $content);
		$content = preg_replace_callback(REGEX_MATH_TEX, array($this, 'parseMathTex'), $content);
	    return $content;
	}
}
