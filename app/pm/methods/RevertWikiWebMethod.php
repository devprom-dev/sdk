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
                $object->modify_parms($object_it->getId(), array(
                    'Content' => $changeIt->getHtmlDecoded('Content')
                ));

                $change_parms = array(
                    'Caption' => $object_it->getDisplayName(),
                    'ObjectId' => $object_it->getId(),
                    'EntityName' => $object_it->object->getDisplayName(),
                    'ClassName' => strtolower(get_class($object_it->object)),
                    'ChangeKind' => 'modified',
                    'Content' => sprintf(text(3100),
                                    $changeIt->getDateFormattedShort('RecordCreated') . ' '. $changeIt->getTimeFormat('RecordCreated'),
                                    $changeIt->getRef('Author')->getDisplayName()
                                ),
                    'VisibilityLevel' => 1,
                    'SystemUser' => getSession()->getUserIt()->getId()
                );
                getFactory()->getObject('ObjectChangeLog')->add_parms( $change_parms );
            }
        }
    }
}
