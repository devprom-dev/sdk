<?php

abstract class SetTagsWebMethod extends WebMethod
{
    abstract function getObject();
    abstract function getTagObject();
    abstract function getAttribute();

    function execute_request()
    {
        if ( $_REQUEST['ids'] != '' ) {
            $ids = join(',', preg_split('/-/', $_REQUEST['ids']));
        }
        if ( $ids == '' ) throw new Exception('Object is required');

        if ( array_key_exists('Tag', $_REQUEST) ) {
            $this->attachTag( $ids, $_REQUEST['value'] );
        }

        if ( array_key_exists('RemoveTag', $_REQUEST) ) {
            $this->removeTag( $ids );
        }
    }

    function attachTag( $ids, $value )
    {
        $request_tag = $this->getTagObject();
        $request_tag->removeNotificator( 'EmailNotificator' );

        $request = $this->getObject();
        $request->removeNotificator( 'EmailNotificator' );

        $request_it = $request->getExact( preg_split('/,/', $ids) );
        while ( !$request_it->end() )
        {
            $parms = array (
                $this->getAttribute() => $request_it->getId(),
                'Tag' => $value
            );

            $mapper = new ModelDataTypeMapper();
            $mapper->map( $request_tag, $parms );

            $request_tag_it = $request_tag->getByAK( $request_it->getId(), $parms['Tag'] );
            if ( $request_tag_it->count() < 1 ) {
                $request_tag->add_parms($parms);
            }

            $request_it->moveNext();
        }
    }

    function removeTag( $ids )
    {
        $request_tag = $this->getTagObject();

        $request = $this->getObject();
        $request->removeNotificator( 'EmailNotificator' );

        $request_it = $request->getExact( preg_split('/,/', $ids) );
        while ( !$request_it->end() )
        {
            $request_tag->removeTags( $request_it->getId() );
            $request_it->moveNext();
        }
    }
}