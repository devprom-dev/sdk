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
            'ParentPage' => $parentId,
            'IsDocument' => 1,
            'DocumentVersion' => $options['DocumentVersion']
        );
        if ( $options['State'] != '' ) {
            $parms['State'] = $options['State'];
        }

        return getFactory()->createEntity($this->object, $parms);
    }

    public function storeDocumentContent($documentId, $content)
    {
        return $this->object->modify_parms($documentId, array(
            'Content' => $content
        ));
    }

    public function buildPage($title, $content, $options, $parentId, $documentIt, $sectionNumber = '', $uid = '')
    {
        $registry = $this->object->getRegistryBase();
        if ( $uid != '' ) {
            $pageIt = $registry->Query(
                array(
                    new WikiDocumentFilter($documentIt),
                    new FilterTextExactPredicate('UID', $uid)
                )
            );
            if ( $pageIt->getId() != '' ) {
                $this->object->modify_parms($pageIt->getId(), array(
                    'Caption' => $title,
                    'Content' => $content,
                ));
                return $this->object->getExact($pageIt->getId());
            }
        }
        if ( $sectionNumber != '' ) {
            $pageIt = $registry->Query(
                array(
                    new FilterAttributePredicate('ParentPage', $parentId),
                    new FilterTextExactPredicate('SectionNumber', $sectionNumber),
                    new SortDocumentClause()
                )
            );
            if ( $pageIt->getId() != '' ) {
                $this->object->modify_parms($pageIt->getId(), array(
                    'Caption' => $title,
                    'Content' => $content,
                ));
                return $this->object->getExact($pageIt->getId());
            }
        }
        return $registry->Create(
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
                new WikiDocumentFilter($documentIt),
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
                new WikiDocumentFilter($this->document_it),
                new FilterSearchAttributesPredicate(urldecode(preg_replace('/[_-]/', ' ', $match[1])), array('Caption'))
            )
        );
        if ( $refIt->getId() == '' ) return $match[0];

        $uid = new ObjectUID();
        $info = $uid->getUIDInfo($refIt);
        return 'href="'.$info['url'].'"';
    }
}