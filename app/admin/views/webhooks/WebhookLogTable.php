<?php
include 'WebhookLogList.php';

class WebhookLogTable extends StaticPageTable
{
	function getList() {
		return new WebhookLogList( $this->getObject() );
	}

	function getNewActions() {
		return array();
	}
	
	function getActions() {
		return array();
	}

	function getSortDefault($sort_parm = 'sort') {
        return 'RecordCreated.D';
    }

    function getBulkActions()
    {
        $actions = parent::getBulkActions();
        unset($actions['modify']);
        return $actions;
    }
}
