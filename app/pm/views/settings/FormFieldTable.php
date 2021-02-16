<?php
include "FormFieldList.php";

class FormFieldTable extends DictionaryItemsTable
{
    function getList() {
        return new FormFieldList( $this->getObject() );
    }
}