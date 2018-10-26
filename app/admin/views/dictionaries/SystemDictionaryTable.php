<?php

include "SystemDictionaryList.php";

class SystemDictionaryTable extends StaticPageTable
{
	function getList() {
		return new SystemDictionaryList( $this->getObject() );
	}

	function getFilterActions() {
		return array();
	}

    function getCaption() {
        return translate('Справочники');
    }
}