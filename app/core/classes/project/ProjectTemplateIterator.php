<?php

class ProjectTemplateIterator extends OrderedIterator
{
    function get( $attribute )
    {
        if ( $attribute == 'FileName' ) {
            return basename(parent::get($attribute));
        }
        else {
            return preg_replace_callback('/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, parent::get($attribute));
        }
    }
}