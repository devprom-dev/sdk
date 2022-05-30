<?php

class WrtfCKEditorSupportParser extends WrtfCKEditorHtmlParser
{
    private $router;

    function __construct($objectIt, $router)
    {
        $this->router = $router;
        parent::__construct($objectIt);

        $this->setHrefResolver(
            function($wiki_it) use($router) {
                $parents = explode(',', trim($wiki_it->get('ParentPath'),','));
                if ( count($parents) > 3 ) {
                    $parentIt = $wiki_it->object->getExact($parents[2]);
                    return $router->generate('docs_article',
                        array('article' => $parentIt->getDisplayName(), '_fragment' => $wiki_it->getId()));
                }
                return $router->generate('docs_article', array('article' => $wiki_it->getDisplayName()));
            }
        );

        $this->setReferenceTitleResolver(
            function($info) {
                return $info['caption'];
            }
        );
    }

    function getUidInfo( $uid )
    {
        if ( preg_match('/K-([\d]+)/', $uid, $matches) ) {
            $registry = $this->getObjectIt()->object->getRegistry();
            $registry->setPersisters(array());
            $objectIt = $registry->Query(
                array (
                    new FilterInPredicate($matches[1])
                )
            );
            return array(
                'object_it' => $objectIt,
                'caption' => $objectIt->getDisplayName()
            );
        }
        else {
            return '';
        }
    }
}
