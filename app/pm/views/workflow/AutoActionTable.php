<?php

include "AutoActionList.php";

class AutoActionTable extends PMPageTable
{
    function getList()
    {
        return new AutoActionList( $this->getObject() );
    }
}
