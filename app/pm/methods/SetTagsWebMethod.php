<?php

abstract class SetTagsWebMethod extends WebMethod
{
    abstract function getObject();
    abstract function getTagObject();
    abstract function getAttribute();

    function __construct( $objectIt = null ) {
        $this->objectIt = $objectIt;
    }

    function execute_request()
    {
        if ( array_key_exists('Tag', $_REQUEST) ) {
            $this->attachTag( $this->objectIt->idsToArray(), $_REQUEST['value'] );
        }

        if ( array_key_exists('RemoveTag', $_REQUEST) ) {
            $this->removeTag( $this->objectIt->idsToArray(), \TextUtils::parseIds($_REQUEST['RemoveTag']) );
        }
    }

    function attachTag( $ids, $value )
    {
        $request_tag = $this->getTagObject();
        $request_tag->removeNotificator( 'EmailNotificator' );

        $request = $this->getObject();
        $request->removeNotificator( 'EmailNotificator' );

        $request_it = $request->getExact($ids);
        while ( !$request_it->end() )
        {
            $parms = array (
                $request_tag->getGroupKey() => $request_it->getId(),
                'Tag' => $value
            );

            getFactory()->mergeEntity($request_tag, $parms);

            $request_it->moveNext();
        }
    }

    function removeTag( $ids, $tagIds )
    {
        $request_tag = $this->getTagObject();

        $request = $this->getObject();
        $request->removeNotificator( 'EmailNotificator' );

        $request_it = $request->getExact($ids);
        while ( !$request_it->end() )
        {
            $request_tag->removeTags( $request_it->getId(), $tagIds );
            $request_it->moveNext();
        }
    }

    private $objectIt = null;
}