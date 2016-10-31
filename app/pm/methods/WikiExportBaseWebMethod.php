<?php
include_once SERVER_ROOT_PATH."core/methods/ExportWebMethod.php";

class WikiExportBaseWebMethod extends ExportWebMethod
{
    function url( $page_it = null, $class, $title = '' )
    {
        $objects = is_object($page_it) ? $page_it->getId() : '';

        $entity = is_object($page_it) ?
            (strtolower(get_class($page_it->object)) == 'metaobject' ?
                $page_it->object->getClassName() : get_class($page_it->object)) : '';

        return parent::getJSCall(
            array( 'class' => $class,
                'objects' => $objects,
                'entity' => $entity )
        );
    }
}
