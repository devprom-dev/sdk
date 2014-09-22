<?php

include 'ConnectorList.php';

class ConnectorTable extends PMPageTable
{
    function getList()
    {
        return new ConnectorList( $this->object );
    }

    function getFilterActions()
    {
        return array();
    }
}
