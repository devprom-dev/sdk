<?php
include_once SERVER_ROOT_PATH.'pm/views/import/ImportXmlForm.php';

class ImportProductExcelForm extends ImportXmlForm
{
 	function getAddCaption()
 	{
 		return str_replace('%1', $this->getObject()->getDisplayName(), text(1722));
 	}
}