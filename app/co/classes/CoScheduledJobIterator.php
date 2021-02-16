<?php

class CoScheduledJobIterator extends OrderedIterator
{
    function get( $attribute )
    {
        return preg_replace_callback('/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, parent::get($attribute));
    }

    function getParameters()
    {
        if ( function_exists('json_decode') )
        {
            $json = json_decode( $this->getHtmlDecoded('Parameters'), true );
            return !is_null($json) ? $json : array();
        }
        else
        {
            return array();
        }
    }

    function getType()
    {
        $parms = $this->getParameters();
        return $parms['type'];
    }
}
