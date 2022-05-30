<?php
include "AttachmentsTable.php";
include "AttachmentsPageSettingBuilder.php";

class AttachmentsPage extends PMPage
{
	function __construct()
	{
		parent::__construct();
		getSession()->addBuilder( new AttachmentsPageSettingBuilder() );
	}

	function needDisplayForm() {
 		return false;
 	}

	function getObject() {
		return getFactory()->getObject('AttachmentUnified');
	}

	function getTable() {
		return new AttachmentsTable($this->getObject());
	}
}