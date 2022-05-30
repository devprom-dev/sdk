<?php
include 'WebhookLogTable.php';
include 'WebhookLogForm.php';

class WebhookLogPage extends AdminPage
{
	function getObject() {
		return new \Metaobject('co_WebhookLog');
	}
	
    function getTable() {
        return new WebhookLogTable($this->getObject());
    }

    function getEntityForm() {
        return new WebhookLogForm($this->getObject());
    }
}

