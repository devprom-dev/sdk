<?php

class WrtfCKEditorComparerParser extends WrtfCKEditorPageParser
{
 	function parse( $content = null )
	{
		$content = parent::parse($content);
		
        $content = preg_replace_callback('/<img\s+alt="([^"]+)"[^>]+>/i', array($this, 'parseUMLImage'), $content);
		$content = preg_replace('/<img[^>]+>/i', str_pad(' '.translate('Изображение').' ', 120, '-', STR_PAD_BOTH), $content);

	    return $content;
	}
	
	function parseUMLImage( $match )
	{
		// decode %u0444 symbols
		$json = JsonWrapper::decode('{"t":"'.str_replace('%u', '\u', base64_decode($match[1])).'"}');

		$uml_code = urldecode($json['t']);
		if ( $uml_code == '' ) return $match[0];

		$uml_separator = str_pad(' UML ', 120, '-', STR_PAD_BOTH);
		return
			'<p>'.$uml_separator.'</p>'.
			'<p>'.preg_replace('/[\r\n]/', '<p></p>', $uml_code).'</p>'.
			'<p>'.$uml_separator.'</p>';
	}
}
