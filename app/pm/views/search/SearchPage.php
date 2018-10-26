<?php
include "SearchTable.php";

class SearchPage extends PMPage
{
    function getObject() {
        return getFactory()->getObject('SearchResult');
    }

 	function getTable() {
 		return new SearchTable($this->getObject());
 	}
}
