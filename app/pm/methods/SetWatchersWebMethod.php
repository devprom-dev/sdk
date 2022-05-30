<?php

class SetWatchersWebMethod extends WebMethod
{
    function execute_request()
    {
        if ( $_REQUEST['operation'] == 'Method:SetWatchersWebMethod:Watchers' ) {
            $this->attachWatchers( $_REQUEST['ids'], $_REQUEST['object'], $_REQUEST['value'] );
        }

        if ( $_REQUEST['operation'] == 'Method:SetWatchersWebMethod:RemoveWatchers' ) {
            $this->removeWatchers( $_REQUEST['ids'], $_REQUEST['object'] );
        }
    }

    function attachWatchers( $ids, $className, $value )
    {
        $className = getFactory()->getClass($className);
        if ( !class_exists($className) ) throw new Exception('Unknown class');

        $objectIt = getFactory()->getObject($className)->getExact( \TextUtils::parseIds($ids) );
        while ( !$objectIt->end() )
        {
            getFactory()->getObject('Watcher', $objectIt)->getRegistry()->Merge(array (
                'ObjectId' => $objectIt->getId(),
                'ObjectClass' => strtolower(get_class($objectIt->object)),
                'SystemUser' => $value
            ));

            $objectIt->moveNext();
        }
    }

    function removeWatchers( $ids, $className )
    {
        $className = getFactory()->getClass($className);
        if ( !class_exists($className) ) throw new Exception('Unknown class');

        $watcherIt = getFactory()->getObject('Watcher')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('ObjectId', \TextUtils::parseIds($ids)),
                new FilterAttributePredicate('ObjectClass', strtolower(get_class($className)))
            )
        );

        while( !$watcherIt->end() ) {
            $watcherIt->object->delete($watcherIt->getId());
            $watcherIt->moveNext();
        }
    }
}