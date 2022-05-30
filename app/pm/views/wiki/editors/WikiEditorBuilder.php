<?php

class WikiEditorBuilder
{
    static function build( $class = '' )
    {
		if ( $class != '' && class_exists($class, false) ) return new $class;
        $class = getSession()->getProjectIt()->get('WikiEditorClass');
		return $class != '' && class_exists($class) ? new $class : new WikiRtfCKEditor;
    }
}
