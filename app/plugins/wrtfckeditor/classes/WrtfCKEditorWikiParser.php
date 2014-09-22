<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";

class WrtfCKEditorWikiParser extends WikiParser
{
 	function parse( $content = null )
	{
		return html_entity_decode( parent::parse($content), ENT_QUOTES | ENT_HTML401, 'cp1251' );
	}
}
 