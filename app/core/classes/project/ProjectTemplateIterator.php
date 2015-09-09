<?php

class ProjectTemplateIterator extends OrderedIterator
{
    function get( $attribute )
    {
        return preg_replace_callback('/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, parent::get($attribute));
    }
}