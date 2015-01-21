<?php

class FailedForm extends AjaxForm
{
	function getTemplate()
	{
		return '../../plugins/accountclient/views/templates/failed.tpl.php';
	}
	
	function getRenderParms()
	{
		return array_merge(
				parent::getRenderParms(),
				array (
						'code' => $_REQUEST['ErrorCode']
				)
		);
	}
}
