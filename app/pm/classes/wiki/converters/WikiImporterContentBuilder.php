<?php

class WikiImporterContentBuilder
{
    private $object = null;

    public function __construct( $object ) {
        $this->object = $object;
    }

    public function buildDocument($documentTitle, $documentContent, $parentId)
    {
        return $this->object->getExact($this->object->add_parms(
            array (
                'Caption' => $documentTitle,
                'Content' => $documentContent,
                'ParentPage' => $parentId
            )
        ));
    }

    public function buildPage($title, $content, $options, $parentId)
    {
        return $this->object->getExact($this->object->add_parms(
            array_merge(
                array (
                    'Caption' => $title,
                    'Content' => $content,
                    'ParentPage' => $parentId
                ),
                $options
            )
        ));
    }
}