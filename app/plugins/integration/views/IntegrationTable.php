<?php
include "IntegrationList.php";

class IntegrationTable extends PMPageTable
{
    function getList() {
        return new IntegrationList( $this->getObject() );
    }

    function getFilterActions2() {
        return array();
    }
}
