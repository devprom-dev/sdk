<?php

class FailedForm extends AjaxForm
{
	function getTemplate()
	{
		return '../../plugins/accountclient/views/templates/failed.tpl.php';
	}
	
	function getRenderParms($view)
	{
		return array_merge(
				parent::getRenderParms($view),
				array (
                    'code' => $_REQUEST['ErrorCode']
				)
		);
	}
}
