<?php

class WrtfCKEditorComparerParser extends WrtfCKEditorPageParser
{
 	function parse( $content = null )
	{
		$content = parent::parse($content);

        $content = preg_replace_callback('/<img\s+alt="([^"]+)"[^>]+>/i', array($this, 'parseUMLImage'), $content);
		$content = preg_replace('/@(\w*)/u', '', $content);
		$content = preg_replace('/(&nbsp;|\xC2\xA0)/i', '&nbsp;', $content);
		$content = preg_replace_callback(REGEX_MATH_TEX, array($this, 'parseMathTex'), $content);

	    return $content;
	}
	
	function parseUMLImage( $match )
	{
		// decode %u0444 symbols
		$json = JsonWrapper::decode('{"t":"'.str_replace('%u', '\u', base64_decode($match[1])).'"}');

		$uml_code = urldecode($json['t']);
		if ( $uml_code == '' ) return $match[0];

		return
			'<pre><code language="html">'.
				join('',
					array_map(function($line) {
						return $line.'<br/>';
					}, preg_split('/[\r\n]/',$uml_code))
				).
			'</code></pre>';
	}
}
