<?php
include "WikiConverterBuilderCommon.php";

class WikiConverterRegistry extends ObjectRegistryArray
{
    protected $data = array();
    protected $page = null;

    function setWikiPage( $page ) {
        $this->page = $page;
    }

    function add( $engineClassName, $title, $parameters = array()) {
        $this->data[] = array(
            'entityId' => count($this->data) + 1,
            'Caption' => $title,
            'EngineClassName' => $engineClassName,
            'EngineParameters' => $parameters
        );
    }

    function createSQLIterator( $sql )
    {
        foreach( $this->getBuilders() as $builder ) {
            $builder->build( $this, $this->page );
        }
        return $this->createIterator( array_values($this->data) );
    }

    function getBuilders()
    {
        return array_merge(
            getSession()->getBuilders('WikiConverterBuilder'),
            array (
                new WikiConverterBuilderCommon()
            )
        );
    }
}