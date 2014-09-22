<?php

class TFSBaseIterator extends OrderedIterator
{
    var $source, $keys;

    function setSource($source)
    {
        $this->source = $source;

        if (is_array($source)) {
            $this->keys = array_keys($source);
        } else {
            $this->keys = array();
        }

        $this->moveFirst();
    }

    function count()
    {
        return count($this->source);
    }

    function moveToPos($pos)
    {
        $this->setPos($pos);
    }

    function moveFirst()
    {
        $this->setPos(0);
    }

    function moveNext()
    {
        $this->setPos($this->getPos() + 1);
    }

    function end()
    {
        return $this->getPos() >= $this->count();
    }
}