<?php

include ('SystemSettingForm.php');

class SettingsPage extends AdminPage
{
	var $settings_it;

	function SettingsPage()
	{
		global $model_factory;
			
		$settings = $model_factory->getObject('cms_SystemSettings');
		$this->settings_it = $settings->getAll();

		parent::Page();
	}

	function getTable()
	{
		return null;
	}

	function needDisplayForm()
	{
		return true;
	}

	function getForm()
	{
		$form = new SystemSettingsForm();
		$form->edit( $this->settings_it->getId() );
		 
		return $form;
	}
}
