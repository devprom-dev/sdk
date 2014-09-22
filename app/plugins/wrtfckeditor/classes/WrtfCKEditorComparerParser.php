<?php

class WrtfCKEditorComparerParser extends WrtfCKEditorPageParser
{
 	function parse( $content = null )
	{
		$content = parent::parse($content);
		
        $content = preg_replace_callback('/<img\s+alt="([^"]+)"[^>]+>/i', array($this, 'parseUMLImage'), $content);

	    return $content;
	}
	
	function parseUMLImage( $match )
	{
		// decode %u0444 symbols
		$json = JsonWrapper::decode('{"t":"'.str_replace('%u', '\u', base64_decode($match[1])).'"}');

		return str_replace("\n", '<p/>', 
				htmlentities(
						IteratorBase::utf8towin(
								urldecode($json['t'])
        				),
						ENT_QUOTES | ENT_HTML401, 'windows-1251'
        		)
        );
	}
}
