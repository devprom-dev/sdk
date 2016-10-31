<?php
include "DocumentTemplateList.php";

class DocumentTemplateTable extends PMPageTable
{
	function getList() {
		return new DocumentTemplateList($this->getObject());
	}

	function getFilterActions() {
		return array();
	}

	function getNewActions() {
		return array();
	}
}
