<?php

class RequestImporterContentBuilder
{
    private $object = null;
    private $ids = array();

    public function __construct( $object ) {
        $this->object = $object;
    }

    public function buildDocument($documentTitle, $documentContent, $parentId) {
        return $this->object->getEmptyIterator();
    }

    function getDocumentIt() {
        return $this->object->getExact($this->ids);
    }

    public function buildPage($title, $content, $options, $parentId)
    {
        $objectId = $this->object->add_parms(
            array_merge(
                array (
                    'Caption' => $title,
                    'Description' => $content
                ),
                $options
            )
        );
        $this->ids[] = $objectId;
        return $this->object->getExact($objectId);
    }
}