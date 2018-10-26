<?php

include 'SystemTemplateList.php';

class SystemTemplateTable extends StaticPageTable
{
	function getList() {
		return new SystemTemplateList( $this->getObject() );
	}

	function getNewActions() {
		return array();
	}
	
	function getActions() {
		return array();
	}

    function getCaption() {
        return translate('Тексты');
    }
}
