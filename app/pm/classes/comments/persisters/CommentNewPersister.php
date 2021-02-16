<?php

class CommentNewPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $userId = getSession()->getUserIt()->getId();
 	    if ( $userId == '' ) $userId = '0';

 		return array(
            "( SELECT COUNT(1) FROM ObjectChangeNotification so ".
            "   WHERE so.ObjectId = t.ObjectId ".
            "     AND so.ObjectClass = t.ObjectClass ".
            "     AND so.SystemUser = ".$userId.
            "     AND so.Action = 'commented' AND so.RecordCreated > t.RecordCreated ) IsNew "
        );
 	}

 	function add($object_id, $parms)
    {
        $objectIt = $this->getObject()->getExact($object_id);
        if ( $objectIt->get('PrevComment') != '' ) {
            $this->openParent($objectIt->getRef('PrevComment'));
        }

        parent::add($object_id, $parms);
    }

    function modify($object_id, $parms)
    {
        if ( $parms['Closed'] == 'Y' ) {
            $this->closeChildren(
                $this->getObject()->getExact($object_id)->getThreadIt()
            );
        }
        if ( $parms['Closed'] == 'N' ) {
            $objectIt = $this->getObject()->getExact($object_id);
            if ( $objectIt->get('PrevComment') != '' ) {
                $this->openParent($objectIt->getRef('PrevComment'));
            }
        }
        parent::modify($object_id, $parms);
    }

    function openParent( $objectIt )
    {
        if ( $objectIt->get('Closed') == 'N' ) return;
        $objectIt->object->getRegistry()->Store(
            $objectIt, array('Closed' => 'N')
        );
    }

    function closeChildren( $objectIt )
    {
        while( !$objectIt->end() ) {
            if ( $objectIt->get('Closed') != 'Y' ) {
                $objectIt->object->getRegistry()->Store(
                    $objectIt, array('Closed' => 'Y')
                );
            }
            $objectIt->moveNext();
        }
    }

    function IsPersisterImportant() {
        return true;
    }
}
