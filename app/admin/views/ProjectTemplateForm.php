<?php

class ProjectTemplateForm extends PageForm
{
	function ProjectTemplateForm()
	{
		global $model_factory;

		parent::PageForm( $model_factory->getObject('pm_ProjectTemplate') );
	}

	function IsNeedButtonNew()
	{
		return false;
	}

	function IsNeedButtonCopy()
	{
		return false;
	}
}
