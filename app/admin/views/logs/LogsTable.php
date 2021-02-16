<?php
include 'LogsList.php';

class LogsTable extends StaticPageTable
{
	function getList( $mode = '' ) {
		return new LogsList( $this->getObject() );
	}

	function getNewActions() {
		return array();
	}
	
	function getActions() {
		return array();
	}

    function getCaption() {
        return translate('Логи');
    }
}
