<?php

class WikiEditorBuilder
{
    static function build( $class = '' )
    {
		if ( $class != '' && class_exists($class, false) ) return new $class;
        $class = getSession()->getProjectIt()->get('WikiEditorClass');
        if ( $class == '' ) $class = 'WikiRtfCKEditor';
		return new $class;
    }
}