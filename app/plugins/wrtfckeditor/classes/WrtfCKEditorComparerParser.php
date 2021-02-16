<?php

class WrtfCKEditorComparerParser extends WrtfCKEditorPageParser
{
    private $codeBlocks = array();

 	function parse( $content = null )
	{
		$content = parent::parse($content);

		$callbacks = array(
            CODE_ISOLATE => array($this, 'codeIsolate'),
            '/<img\s([^>]+)>/i' => array($this, 'parseUMLImage'),
            REGEX_INCLUDE_PAGE => array($this, 'parseIncludePageCallback'),
            REGEX_MATH_TEX => array($this, 'parseMathTex'),
            CODE_RESTORE => array($this, 'codeRestore'),
            REGEX_COMMENTS => array($this, 'removeComments'),
        );
        if ( function_exists('preg_replace_callback_array') ) {
            $content = preg_replace_callback_array($callbacks, $content);
        }
        else {
            foreach( $callbacks as $regexp => $callback ) {
                $content = preg_replace_callback($regexp, $callback, $content);
            }
        }

        $content = str_replace('&nbsp;', '&nbsp;', $content);
        $content = str_replace('\xC2\xA0', '&nbsp;', $content);

        return $content;
	}
	
	function parseUMLImage( $match )
	{
	    if ( !preg_match('/(alt|uml)="([^"]+)"/i', $match[1], $umlMatches) ) return $match[0];

		// decode %u0444 symbols
		$json = JsonWrapper::decode('{"t":"'.str_replace('%u', '\u', base64_decode($umlMatches[2])).'"}');
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

    function codeRestore( $match ) {
 	    $blocks = $this->getCodeBlocks();
        return '<code'.$match[1].'>'.
            join('',
                array_map(function($line) {
                    return $line.'<br/>';
                }, preg_split('/[\r\n]/',$blocks[$match[2] - 1]))
            )
        .'</code>';
    }

    function removeComments( $match )
    {
 	    return $match[2];
    }
}
