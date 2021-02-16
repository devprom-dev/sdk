<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterContentBuilder.php";

class RequestImporterContentBuilder extends WikiImporterContentBuilder
{
    private $ids = array();

    public function buildDocument($documentTitle, $documentContent, $options, $parentId) {
        if ( $documentContent == '' ) {
            return $this->getObject()->getEmptyIterator();
        }
        return $this->buildPage(
            $documentTitle, $documentContent, $options, $parentId, $this->getObject()->getEmptyIterator()
        );
    }

    function getDocumentIt() {
        return $this->getObject()->getExact($this->ids);
    }

    public function buildPage($title, $content, $options, $parentId, $documentIt, $sectionNumber = '', $uid = '')
    {
        $objectId = $this->getObject()->add_parms(
            array_merge(
                array (
                    'Caption' => $title,
                    'Description' => $content
                ),
                $options
            )
        );
        $this->ids[] = $objectId;
        return $this->getObject()->getExact($objectId);
    }

    public function parsePages($pageIt)
    {
    }
}