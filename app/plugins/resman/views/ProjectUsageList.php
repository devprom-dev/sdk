<?php

include "ResourceList.php";

class ProjectUsageList extends ResourceList
{
 	function getBaseObject()
 	{
 		global $model_factory;
 		
		$method = new ResourceFilterViewWebMethod();
		$method->setFilter( $this->getFiltersName() );

		switch ( $method->getValue() )
		{
			case 'roles':
				$cache = $model_factory->getObject('ProjectRole');
				$cache->addFilter( new ProjectRoleInheritedFilter() );
				break;
				
			default:
				return parent::getBaseObject();
		}
		
		return $cache;
 	}
 	 	
	function getColumnFields()
	{
		return array();
	}

	function IsNeedToDisplay( $attr )
	{
		switch ( $attr )
		{
			case 'Details':
				return true;
				
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}
	
	function getColumns()
	{
		$this->object->addAttribute('Details', '', text('ee28'), true);
		
		return parent::getColumns();
	}

	function drawCell( $object_it, $attr )
	{
		global $model_factory, $project_it;
		
		$report = $model_factory->getObject('PMReport');
		$report_it = $report->getExact('currenttasks');
		
		switch ( $attr )
		{
			case 'Details':
				switch ( $this->object->getEntityRefName() )
				{
					case 'cms_User':
						
						if ( getFactory()->getAccessPolicy()->can_read($report_it) )
						{
							echo '<div align="left"><a title="'.text('ee26').'" href="'.$report_it->getUrl().'&taskassignee='.$object_it->getId().'">'.text('ee27').'</a></div>';
						}
						
						break;
						
					case 'pm_ProjectRole':
						$type = $model_factory->getObject('TaskType');
						$type_it = $type->getForRole( $object_it );
						
						if ( getFactory()->getAccessPolicy()->can_read($report_it) )
						{
							echo '<div align="left"><a title="'.text('ee26').'" href="'.$report_it->getUrl().'&tasktype='.join(',',$type_it->idsToArray()).'">'.text('ee27').'</a></div>';
						}
						break;
				}
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}
}
