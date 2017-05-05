<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewSpentTimeWebMethod extends FilterWebMethod
{
 	function getCaption()
 	{
 		return translate('Вид');
 	}
 	
 	function getValues()
 	{
  		$values = array (
 			'participants' => translate('Участники'),
 			'projects' => text('projects.name'),
 			'tasks' => translate('Задачи')
		);

        if ( getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('pm_ChangeRequest')) ) {
            $values['issues'] = translate('Пожелания');
        }

 		return $values;
	}
	
	function getValue()
	{
	    $value = parent::getValue();

	    if ( $value == '' ) {
            $values = $this->getValues();
            if ( array_key_exists('issues', $values) ) return 'issues';
            return 'tasks';
        }
	    
	    return $value;
	}
	
	function getStyle()
	{
		return 'width:145px;';
	}
	
	function getValueParm()
	{
		return 'view';
	}
	
 	function getType()
 	{
 		return 'singlevalue';
 	}
}