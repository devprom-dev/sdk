<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class RevertWikiWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return translate('Отменить');
    }

    function url( $object_it, $changeLogIt, $revisionBeforeChanges )
    {
        return parent::getJSCall( array(
            'wiki' => $object_it->getId(),
            'class' => get_class($object_it->object),
            'logid' => $changeLogIt->getId(),
            'revision' => $revisionBeforeChanges
        ));
    }

    function execute_request()
    {
        $class = getFactory()->getClass($_REQUEST['class']);
        if ( !class_exists($class) ) return;

        $object = getFactory()->getObject($class);
        $object_it = $object->getExact( $_REQUEST['wiki'] );

        if ( $object_it->getId() != '' && getFactory()->getAccessPolicy()->can_modify($object_it) ) {
            $changeIt = getFactory()->getObject('WikiPageChange')->getExact($_REQUEST['revision']);
            if ( $changeIt->getId() != '' ) {
                $object_it->Revert($changeIt);
                $log_it = getFactory()->getObject('ChangeLog')->getExact(\TextUtils::parseIds($_REQUEST['logid']));
                while( !$log_it->end() ) {
                    $log_it->object->delete($log_it->getId());
                    $log_it->moveNext();
                }
            }
        }
    }
}
