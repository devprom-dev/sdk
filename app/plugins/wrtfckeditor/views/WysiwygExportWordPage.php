<?php

include "WysiwygExportWordTable.php";

class WysiwygExportWordPage extends PMPage
{
    function getObject()
    {
        return getFactory()->getObject('cms_SystemSettings');
    }

    function getTable()
    {
        return new WysiwygExportWordTable( $this->getObject() );
    }
}