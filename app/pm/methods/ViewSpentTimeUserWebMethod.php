<?php

class ViewSpentTimeUserWebMethod extends FilterAutoCompleteWebMethod
{
 	function getCaption()
 	{
 		return translate('Участник');
 	}
 	
 	function ViewSpentTimeUserWebMethod()
 	{
 		global $model_factory;
 		
 		$user = $model_factory->getObject('cms_User');
 		parent::FilterAutoCompleteWebMethod( $user, $this->getCaption() );
 	}
 	
	function getStyle()
	{
		return 'width:145px;';
	}
	
	function getValueParm()
	{
		return 'participant';
	}
}
