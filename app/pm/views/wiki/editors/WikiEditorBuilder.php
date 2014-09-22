<?php

include "WikiSyntaxEditor.php";

class WikiEditorBuilder
{
    static function build( $class = '' )
    {
		if ( $class == '' ) $class = getSession()->getProjectIt()->get('WikiEditorClass');

		if ( class_exists($class, false) ) return new $class;
		
		return new WikiSyntaxEditor;
    }
}