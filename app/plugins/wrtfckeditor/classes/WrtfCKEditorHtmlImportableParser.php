<?php

class WrtfCKEditorHtmlImportableParser extends WrtfCKEditorHtmlParser
{
    function parseUMLImage( $match )
    {
        if ( !preg_match('/(alt|uml)="([^"]+)"/i', $match[1], $umlMatches) ) return $match[0];

        // decode %u0444 symbols
        $json = JsonWrapper::decode('{"t":"'.str_replace('%u', '\u', base64_decode($umlMatches[2])).'"}');
        $uml_code = urldecode($json['t']);
        if ( $uml_code == '' ) return $match[0];

        $srcMatches = array();
        preg_match('/src="([^"]+)"/i', $match[1], $srcMatches);

        return
            '<p>@startuml</p>'.
            '<p><img src="'.$srcMatches[1].'"></p>'.
            join('',
                array_map(function($line) {
                    return '<p>' . $line.'</p>';
                }, preg_split('/[\r\n]/',$uml_code))
            ).
            '<p>@enduml </p>';
    }

    function parseMathTex( $match ) {
        $url = EnvironmentSettings::getMathJaxServer();
        $mathFormula = trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML401, APP_ENCODING ));
        $mathText =  join('',
            array_map(function($line) {
                return '<p>' . $line . '</p>';
            }, preg_split('/[\r\n]/', $mathFormula))
        );
        return
            '<p>@startmath </p>'.
            '<p><img src="'.$url.rawurlencode($mathFormula).'"></p>'.
            '<p>' . $mathText . '</p>' .
            '<p>@endmath </p>';
    }

    function codeRestore( $match ) {
        $blocks = $this->getCodeBlocks();
        return '<p>@startcode,'.trim(array_pop(explode('=',$match[1])),'"').' </p>'.
            join('',
                array_map(function($line) {
                    return '<p>' . $line . '</p>';
                }, explode(PHP_EOL,$blocks[$match[2] - 1]))
            )
            .'<p>@endcode </p>';
    }
}
