<?php
namespace Devprom\ProjectBundle\Service\Model;

class ModelChangeNotification
{
    function clearAll( $objectIt )
    {
        if ( !is_object($objectIt) ) return;
        if ( $objectIt->getId() == '' ) return;

        \DAL::Instance()->Query(
            " DELETE FROM ObjectChangeNotification 
               WHERE ObjectId = ".$objectIt->getId()." 
                 AND ObjectClass = '".get_class($objectIt->object)."' "
        );
    }

    function clearUser( $objectIt, $userIt, $exceptActions = array() )
    {
        if ( !is_object($objectIt) ) return;
        if ( $objectIt->getId() == '' ) return;
        if ( !is_object($userIt) ) return;
        if ( $userIt->getId() == '' ) return;

        \DAL::Instance()->Query(
            " DELETE FROM ObjectChangeNotification 
                   WHERE ObjectId = ".$objectIt->getId()." 
                     AND ObjectClass = '".get_class($objectIt->object)."' 
                     AND SystemUser = ".$userIt->getId().
                    (count($exceptActions) > 0 ? " AND Action NOT IN ('".join("','", $exceptActions)."') " : "")
        );
    }
}