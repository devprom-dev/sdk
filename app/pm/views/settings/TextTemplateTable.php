<?php
include "TextTemplateList.php";

class TextTemplateTable extends DictionaryItemsTable
{
    function getList() {
        return new TextTemplateList( $this->getObject() );
    }

    function getSortAttributeClause($field) {
        return new TextTemplateSortClause();
    }
}