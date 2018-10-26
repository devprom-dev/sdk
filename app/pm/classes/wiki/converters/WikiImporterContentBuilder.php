<?php

class WikiImporterContentBuilder
{
    private $object = null;

    public function __construct( $object ) {
        $this->object = $object;
    }

    public function getObject() {
        return $this->object;
    }

    public function buildDocument($documentTitle, $documentContent, $options, $parentId)
    {
        $parms = array (
            'Caption' => $documentTitle,
            'Content' => $documentContent,
            'ParentPage' => $parentId
        );
        if ( $options['State'] != '' ) {
            $parms['State'] = $options['State'];
        }
        return $this->object->getRegistryBase()->Create($parms);
    }

    public function buildPage($title, $content, $options, $parentId)
    {
        return $this->object->getRegistryBase()->Create(
            array_merge(
                array (
                    'Caption' => $title,
                    'Content' => $content,
                    'ParentPage' => $parentId
                ),
                $options
            )
        );
    }

    public function parsePages( $documentIt )
    {
        $this->document_it = $documentIt;
        $pageIt = $this->getObject()->getRegistry()->Query(
            array(
                new WikiDocumentWaitFilter($documentIt->getId()),
                new SortDocumentClause()
            )
        );
        while( !$pageIt->end() ) {
            $this->parsePage($pageIt);
            $pageIt->moveNext();
        }
    }

    protected function parsePage( $pageIt )
    {
        $content = $pageIt->getHtmlDecoded('Content');
        $result = preg_replace_callback(HTML_IMPORT_ANCHOR, array($this, 'parsePageAnchors'), $content);

        if ( $result != $content ) {
            $pageIt->object->getRegistry()->Store( $pageIt,
                array(
                    'Content' => $result
                )
            );
        }
    }

    function parsePageAnchors( $match )
    {
        $refIt = $this->getObject()->getRegistry()->Query(
            array(
                new FilterAttributePredicate('DocumentId', $this->document_it->getId()),
                new FilterSearchAttributesPredicate(urldecode(preg_replace('/[_-]/', ' ', $match[1])), array('Caption'))
            )
        );
        if ( $refIt->getId() == '' ) return $match[0];

        $uid = new ObjectUID();
        $info = $uid->getUIDInfo($refIt);
        return 'href="'.$info['url'].'"';
    }
}