<?php
use \InlineStyle\InlineStyle;

class WikiPageStylePersister extends ObjectSQLPersister
{
	function map( &$parms )
	{
		if ( $parms['UserField3'] == '' || $parms['Content'] == '' ) return;

		$was_state = libxml_use_internal_errors(true);

		$htmldoc = new InlineStyle(
			"<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>".
			"<body>".$parms['Content']."</body>"
		);

		$htmldoc->applyStylesheet(
			file_get_contents(SERVER_ROOT_PATH.'styles/wysiwyg/msword.css')
		);

		if ( file_exists(SERVER_ROOT_PATH.'plugins/wrtfckeditor/ckeditor/custom.css') ) {
			$htmldoc->applyStylesheet(
				file_get_contents(SERVER_ROOT_PATH.'plugins/wrtfckeditor/ckeditor/custom.css')
			);
		}

		$page_css = preg_replace('/<\!--|-->/', '', $parms['UserField3']);
		$htmldoc->applyStylesheet($page_css);

		$html = $htmldoc->getHTML();
		libxml_clear_errors();
		libxml_use_internal_errors($was_state);

		$parms['Content'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|body|head|meta))[^>]*>\s*~i', '', $html);
		$parms['UserField3'] = '';
	}
}
