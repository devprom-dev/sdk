<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterViewWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Вид');
 	}

 	function getValues()
 	{
  		return array (
 			'users' => translate('Участники'),
 			'roles' => translate('Роли'),
 			'projects' => translate('Проекты')
 			);
	}
	
	function getStyle()
	{
		return 'width:140px;';
	}
	
	function getValueParm()
	{
		return 'viewpoint';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
	
	function getValue()
	{
		$value = parent::getValue();
		
		if ( in_array($value, array('all','')) )
		{
			return 'projects';
		}
		
		return $value;
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterScaleWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Масштаб');
 	}

 	function getValues()
 	{
  		return array (
 			'month' => translate('По месяцам'),
 			'quarter' => translate('По кварталам'),
 			'week' => translate('По неделям')
 			);
	}
	
	function getStyle()
	{
		return 'width:155px;';
	}
	
	function getValueParm()
	{
		return 'scale';
	}

 	function getType()
	{
		return 'singlevalue';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterYearWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Год');
 	}

 	function getValues()
 	{
 		$sql = " SELECT DISTINCT YEAR(StartDate) StartDate FROM pm_CalendarInterval ORDER BY StartDate ASC";
 		
 		$interval_it = getFactory()->getObject('pm_Project')->createSQLIterator( $sql );
 		
 		while ( !$interval_it->end() )
 		{
			$values[' '.$interval_it->get('StartDate')] = $interval_it->get('StartDate');

			$interval_it->moveNext();
 		}
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:40px;';
	}
	
	function getValueParm()
	{
		return 'year';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
	
	function getValue()
	{
		$value = parent::getValue();

		if ( in_array($value, array('all','')) )
		{
			$value = date('Y');
		}
		
		return $value;
	}

	function getFreezeMethod()
	{
		return null;
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterMonthWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Месяц');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'all' => translate('Все')
 			);
 			
		$date = $model_factory->getObject('DateMonth');
		$date_it = $date->getAll();
		
		while ( !$date_it->end() )
		{
			$values[$date_it->getId()] = $date_it->getDisplayName();
			$date_it->moveNext();
		}
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:105px;';
	}
	
	function getValueParm()
	{
		return 'month';
	}

 	function getType()
	{
		return 'singlevalue';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterUserWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return text('ee25');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'all' => translate('Все')
 			);

		$group = $model_factory->getObject('UserGroup');
		$group_it = $group->getAll();
		
		while ( !$group_it->end() )
		{
			$values[$group_it->getId()] = $group_it->getDisplayName();
			$group_it->moveNext();
		}

 		return $values;
	}
	
	function getStyle()
	{
		return 'width:165px;';
	}
	
	function getValueParm()
	{
		return 'usergroup';
	}

 	function getType()
	{
		return 'singlevalue';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterRoleWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return text('ee30');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'all' => translate('Все')
 			);

		$role = $model_factory->getObject('ProjectRoleBase');
		$role_it = $role->getAll();
		
		while ( !$role_it->end() )
		{
			$values[$role_it->getId()] = $role_it->getDisplayName();
			$role_it->moveNext();
		}

 		return $values;
	}
	
	function getStyle()
	{
		return 'width:115px;';
	}
	
	function getValueParm()
	{
		return 'role';
	}
 
	function getType()
	{
		return 'singlevalue';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterFormatWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Формат');
 	}

 	function getValues()
 	{
  		return array (
 			'graphical' => translate('Графический'),
 			'hours' => translate('Часы')
 			);
	}
	
	function getStyle()
	{
		return 'width:145px;';
	}
	
	function getValueParm()
	{
		return 'format';
	}

 	function getType()
	{
		return 'singlevalue';
	}
	
	function getValue()
	{
		$value = parent::getValue();
		
		if ( in_array($value, array('all','')) )
		{
			return 'hours';
		}
		
		return $value;
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterDividerWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Разбивка');
 	}

 	function getValues()
 	{
  		return array (
 			'none' => translate('Нет'),
 			'users' => translate('По участникам'),
 			'projects' => translate('По проектам'),
 			'roles' => translate('По ролям')
  		);
	}
	
	function getStyle()
	{
		return 'width:145px;';
	}
	
	function getValueParm()
	{
		return 'divider';
	}

 	function getType()
	{
		return 'singlevalue';
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ResourceFilterProjectWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Портфель');
 	}

 	function getValues()
 	{
		$group_it = getFactory()->getObject('Portfolio')->getAll();
		
		while ( !$group_it->end() )
		{
			$values[$group_it->getId()] = $group_it->getDisplayName();
			
			$group_it->moveNext();
		}

 		return $values;
	}
	
	function getStyle()
	{
		return 'width:185px;';
	}
	
	function getValueParm()
	{
		return 'portfolio';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
	
	function getValue()
	{
		$value = parent::getValue();
		
		if ( $value == '' ) return 'all';
		
		return $value;
	}
 }
