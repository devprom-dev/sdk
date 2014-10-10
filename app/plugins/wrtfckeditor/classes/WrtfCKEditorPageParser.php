<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";

use \InlineStyle\InlineStyle;

class WrtfCKEditorPageParser extends WikiParser
{
    function parse( $content = null )
    {
        global $wiki_parser;

        $wiki_parser = $this;

		$content = preg_replace_callback(REGEX_UID, array($this, 'parseUidCallback'), $content);

		$content = preg_replace_callback(REGEX_INCLUDE_PAGE, array($this, 'parseIncludePageCallback'), $content);
		
        $content = preg_replace_callback('/\s+src="([^"]*)"/i', preg_image_src_callback, $content);
        
        $was_state = libxml_use_internal_errors(true);
        
        $htmldoc = new InlineStyle(
            "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>".
            "<body>".IteratorBase::wintoutf8($content)."</body>"
        );

        $object_it = $this->getObjectIt();

        if ( is_object($object_it) && $object_it->get('UserField3') != '' )
        {
            $page_css = $object_it->getHtmlDecoded('UserField3');
            
            $page_css = preg_replace('/<\!--|-->/', '', $page_css);
            
            $htmldoc->applyStylesheet($page_css);
        }

    	if ( file_exists(SERVER_ROOT_PATH.'plugins/wrtfckeditor/ckeditor/custom.css') )
		{
		    $htmldoc->applyStylesheet(file_get_contents(SERVER_ROOT_PATH.'plugins/wrtfckeditor/ckeditor/custom.css'));
		}
        
        $htmldoc->applyStylesheet(file_get_contents(SERVER_ROOT_PATH.'styles/wysiwyg/msword.css'));
		
        $html = IteratorBase::utf8towin($htmldoc->getHTML());
        
        libxml_clear_errors();
        
        libxml_use_internal_errors($was_state);

        return preg_replace('~<(?:!DOCTYPE|/?(?:html|body|head|meta))[^>]*>\s*~i', '', $html);
    }
}
