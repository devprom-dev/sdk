<?php

include ('SystemSettingForm.php');

class SettingsPage extends AdminPage
{
	var $settings_it;

	function __construct()
	{
		$settings = getFactory()->getObject('cms_SystemSettings');
		$this->settings_it = $settings->getAll();

		parent::__construct();
	}

	function getTable()
	{
		return null;
	}

	function needDisplayForm()
	{
		return true;
	}

	function getEntityForm()
	{
		$form = new SystemSettingsForm();
		$form->edit( $this->settings_it->getId() );
		 
		return $form;
	}
}
