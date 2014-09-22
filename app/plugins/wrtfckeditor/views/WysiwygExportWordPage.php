<?php

include "WysiwygExportWordTable.php";

class WysiwygExportWordPage extends PMPage
{
    function getObject()
    {
        return getFactory()->getObject('entity');
    }

    function getTable()
    {
        return new WysiwygExportWordTable( $this->getObject() );
    }
}