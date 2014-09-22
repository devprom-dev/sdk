<?php

include ('LoginForm.php');

class LoginPage extends CoPage
{
 	function getTable()
 	{
 		global $model_factory;
 		
 		return new LoginForm($model_factory->getObject('cms_User'));
 	}
}
