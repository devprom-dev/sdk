<?php

class WrtfCKEditorSupportParser extends WrtfCKEditorPageParser
{
    private $router;

    function __construct($objectIt, $router)
    {
        $this->router = $router;
        parent::__construct($objectIt);

        $this->setHrefResolver(function($wiki_it) {
            return '#'.$wiki_it->getId();
        });
        $this->setReferenceTitleResolver(function($info) {
            return $info['caption'];
        });
    }

    function getUidInfo( $uid )
    {
        if ( preg_match('/K-([\d]+)/', $uid, $matches) ) {
            $registry = $this->getObjectIt()->object->getRegistry();
            $registry->setPersisters(array());
            return array(
                'object_it' => $registry->Query(
                    array (
                        new FilterInPredicate($matches[1])
                    )
                )
            );
        }
        else {
            return '';
        }
    }
}
