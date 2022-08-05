<?php
include "ExampleEntityList.php";

class ExampleEntityTable extends SettingsTableBase
{
    function getList( $mode = '' ) {
        return new ExampleEntityList( $this->getObject() );
    }
}
