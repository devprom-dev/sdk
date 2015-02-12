<?php

include "ServicePayedList.php";

class ServicePayedTable extends PageTable
{
    function getList()
    {
        return new ServicePayedList( $this->getObject() );
    }
}
