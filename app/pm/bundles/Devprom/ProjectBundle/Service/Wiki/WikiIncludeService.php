<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiIncludeService
{
    private $factory = null;
    private $session = null;

    function __construct( $factory, $session ) {
        $this->factory = $factory;
        $this->session = $session;
    }

    function includePagesInto( $object, $pages, $parentPage, $orderNum )
    {
        $ids = \TextUtils::parseIds($pages);
        if ( count($ids) < 1 ) return;

        $include_it = $object->getRegistryBase()->Query(
            array (
                $object instanceof \Requirement
                    ? new \FilterInPredicate($ids)
                    : new \ParentTransitiveFilter($ids),
                new \ProjectAccessibleVpdPredicate(),
                new \SortDocumentClause()
            )
        );

        $ids = $include_it->idsToArray();
        if ( count($ids) < 1 ) return;

        if ( $orderNum == '' ) $orderNum = 10;

        $include_it = $object->getRegistry()->Query(
            array (
                new \FilterInPredicate($ids),
                new \SortDocumentClause()
            )
        );

        $uid = new \ObjectUID();
        $maps = array();
        while( !$include_it->end() ) {
            $id = getFactory()->createEntity($object, array (
                    'Caption' => $include_it->getHtmlDecoded('Caption'),
                    'Content' => "{{".$uid->getObjectUID($include_it)."}}",
                    'Includes' => $include_it->getId(),
                    'PageType' => $include_it->get('PageType'),
                    'IsTemplate' => 0,
                    'ParentPage' => $maps[$include_it->get('ParentPage')] != ''
                        ? $maps[$include_it->get('ParentPage')]
                        : $parentPage,
                    'OrderNum' => $orderNum
                ))->getId();
            $maps[$include_it->getId()] = $id;

            $orderNum += 10;
            $include_it->moveNext();
        }

        return true;
    }
}