<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterContentBuilder.php";

class WikiImporterListBuilder extends WikiImporterContentBuilder
{
    public function buildDocument($documentTitle, $documentContent, $options, $parentId)
    {
        $parms =  array (
            'Caption' => $documentTitle,
            'Content' => $documentContent
        );
        if ( $options['State'] != '' ) {
            $parms['State'] = $options['State'];
        }
        if ( $documentContent != '' ) {
            $this->getObject()->add_parms($parms);
        }
        return $this->getObject()->getEmptyIterator();
    }

    public function buildPage($title, $content, $options, $parentId)
    {
        return $this->getObject()->getExact($this->getObject()->add_parms(
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