<?php

class IntegrationDummyChannel extends IntegrationChannel
{
    public function getItems( $timestamp, $limit ) {
        return array();
    }

    public function readItem($mapping, $class, $id, $parms = array())
    {
    }

    public function writeItem($mapping, $class, $id, $data, $queueItem)
    {
    }

    public function deleteItem($mapping, $class, $id)
    {
    }

    public function buildDictionaries()
    {
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
    }
}