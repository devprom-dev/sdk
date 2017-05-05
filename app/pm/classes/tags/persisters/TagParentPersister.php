<?php

class TagParentPersister extends ObjectSQLPersister
{
    private $tagIds = array();

    function map(& $parms)
    {
        if ( $parms['Tag'] == '' ) return;
        if ( is_numeric($parms['Tag']) ) return;

        $tag = getFactory()->getObject('Tag');
        foreach( preg_split('/[,;:]/', $parms['Tag']) as $tagName ) {
            $tag_it = $tag->getByRef('Caption', $tagName);
            $this->tagIds[$tagName] =
                $tag_it->getId() > 0
                    ? $tag_it->getId()
                    : $tag->add_parms( array('Caption' => $tagName) ) ;
        }
        $parms['Tag'] = array_shift($this->tagIds);
    }

    function add($object_id, $parms)
    {
        $tagRegistry = $this->getObject()->getRegistry();
        $tagRegistry->setPersisters(array());

        $ids = $this->tagIds;
        $this->tagIds = array();
        foreach( $ids as $tagId ) {
            $parms['Tag'] = $tagId;
            $tagRegistry->Create($parms);
        }
    }
}
