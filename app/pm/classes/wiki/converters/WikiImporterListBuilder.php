<?php

class WikiImporterListBuilder
{
    private $object = null;

    public function __construct( $object ) {
        $this->object = $object;
    }

    public function buildDocument($documentTitle, $documentContent, $parentId)
    {
        if ( $documentContent != '' ) {
            $this->object->add_parms(
                array (
                    'Caption' => $documentTitle,
                    'Content' => $documentContent
                )
            );
        }
        return $this->object->getEmptyIterator();
    }

    public function buildPage($title, $content, $options, $parentId)
    {
        return $this->object->getExact($this->object->add_parms(
            array_merge(
                array (
                    'Caption' => $title,
                    'Content' => $content
                ),
                $options
            )
        ));
    }
}