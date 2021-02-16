<?php

class TagParentPersister extends ObjectSQLPersister
{
    private $tagIds = array();

    function map(& $parms)
    {
        if ( $parms['Tag'] == '' ) return;

        $tag = getFactory()->getObject('Tag');
        if ( is_numeric($parms['Tag']) ) {
            $tagIt = $tag->getExact($parms['Tag']);
            if ( $tagIt->getId() != '' ) {
                $this->tagIds[] = $tagIt->getId();
                return;
            }
        }

        $tagParms = array();
        if ( getSession()->getProjectIt()->IsPortfolio() ) {
            $tagParms['VPD'] = '';
        }

        foreach( preg_split('/[,;]/', $parms['Tag']) as $tagName ) {
            $tagIt = $tag->getRegistry()->Query(array(
                new FilterAttributePredicate('Caption', $tagName ),
                new FilterBaseVpdPredicate()
            ));
            if ( $tagIt->getId() == '' && getFactory()->getAccessPolicy()->can_create($tag) ) {
                $tagIt = $tag->getRegistry()->Create(array(
                    'Caption' => $tagName
                ));
            }
            $this->tagIds[$tagName] = $tagIt->getId();
        }

        $parms['Tag'] = array_shift($this->tagIds);
    }
}
