<?php
 
class LoginForm extends AjaxForm
{
	function getWidth()
	{
		return '40%';
	}
	
	function getTemplate()
	{
		return "co/FormAsyncLogin.php";
	}
	
	function getActions()
	{
	    return array ( 
            array ( 'name' => text(1330), 'url' => "/recovery" )
        );
	}
}