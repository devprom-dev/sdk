<?php

class AdminForm extends AjaxForm
{
	function AdminForm( $object )
	{
		parent::AjaxForm( $object );
	}

	function getSite()
	{
		return 'admin';
	}
	
	function getWidth()
	{
		return '100%';
	}

	function IsCentered()
	{
		return false;
	}
}
