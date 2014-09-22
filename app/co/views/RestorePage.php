<?php

include('RestoreForm.php');

class ForgetPasswordPage extends CoPage
{
 	function getTable()
 	{
 		global $model_factory;
 		
		return new ForgetPasswordForm( $model_factory->getObject('cms_User') );
 	}
}
