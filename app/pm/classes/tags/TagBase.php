<?php
include "sorts/TagCaptionSortClause.php";

class TagBase extends Metaobject
{
 	function getGroupKey()
 	{
 	}

    function getByAK( $object_id, $tag_id ) {
        return $this->getByRefArray( array(
            $this->getGroupKey() => $object_id,
            'Tag' => $tag_id
        ));
    }

    function bindToObject( $object_id, $tag_id )
    {
        $this->add_parms(array(
            $this->getGroupKey() => $object_id,
            'Tag' => $tag_id
        ));
    }

    function getVpds()
    {
        return array_merge(
            parent::getVpds(),
            array(
                ''
            )
        );
    }

    function removeTags( $objectId, $tagIds = array() )
    {
        if ( $objectId < 1 ) return;
        $tagIt = $this->getRegistry()->Query(
            array(
                new FilterAttributePredicate($this->getGroupKey(), $objectId),
                new FilterAttributePredicate('Tag', $tagIds)
            )
        );
        while( !$tagIt->end() ) {
            $this->delete($tagIt->getId());
            $tagIt->moveNext();
        }
    }
}