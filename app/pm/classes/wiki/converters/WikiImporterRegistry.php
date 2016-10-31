<?php

class WikiImporterRegistry extends ObjectRegistryArray
{
    protected $data = array();
    protected $page = null;

    function setWikiPage( $page ) {
        $this->page = $page;
    }

    function add( $engineClassName ) {
        $this->data[] = array(
            'entityId' => count($this->data) + 1,
            'EngineClassName' => $engineClassName
        );
    }

    function createSQLIterator( $sql )
    {
        foreach( $this->getBuilders() as $builder ) {
            $builder->build( $this, $this->page );
        }
        return $this->createIterator( array_values($this->data) );
    }

    function getBuilders() {
        return getSession()->getBuilders('WikiImporterBuilder');
    }
}