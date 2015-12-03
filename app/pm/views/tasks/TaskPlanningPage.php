<?php

include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelCommonBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderTasks.php";

include "TaskForm.php";
include "TaskBulkForm.php";
include "IterationBurndownSection.php";
include "WorkloadSection.php";
include "TaskTable.php";
include "WorkItemTable.php";
include SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";

class TaskPlanningPage extends PMPage
{
 	function TaskPlanningPage()
 	{
 		global $_REQUEST, $model_factory;

 		getSession()->addBuilder( new TaskViewModelCommonBuilder() );
 		getSession()->addBuilder( new BulkActionBuilderTasks() );
 		
 		parent::PMPage();
 		
 		if ( $_REQUEST['view'] == 'chart' ) return;
 		
 		if ( $this->needDisplayForm() )
 		{
			$this->addInfoSection( new PageSectionAttributes($this->getObject(),'deadlines',translate('Сроки')) );
			$this->addInfoSection( new PageSectionAttributes($this->getObject(),'source-issue',translate('Пожелание')) );
			$this->addInfoSection( new PageSectionAttributes($this->getObject(),'trace',translate('Трассировки')) );

 			$object_it = $this->getObjectIt();
 			if ( is_object($object_it) && $object_it->count() > 0 )
 			{
			    $this->addInfoSection( new PageSectionComments($object_it) );
 				$this->addInfoSection( new StatableLifecycleSection( $object_it ) );
 				$this->addInfoSection( new PMLastChangesSection ( $object_it ) );
 			}
 		}
 		elseif ( $_REQUEST['mode'] != 'bulk' )
 		{
 		    if ( $_REQUEST['view'] == 'board' ) $this->addInfoSection( new FullScreenSection() );
 			
 		    $workload_section = new WorkloadSection();
 			if (getSession()->getProjectIt()->getMethodologyIt()->HasReleases())
 			{
 			 	if ( getFactory()->getObject('PMReport')->getExact('iterationburndown')->getId() != '' ) {
	 				$this->addInfoSection( new IterationBurndownSection () );
	 			}
 				if ( count($workload_section->getData()) > 0 ) $this->addInfoSection($workload_section);
 			}
 		}
 	}
 	
 	function getObject()
 	{
		if ( $this->getSessionReportName() == 'mytasks' ) {
			$object = getFactory()->getObject('WorkItem');
		} else {
			$object = getFactory()->getObject('Task');
		}

	    foreach(getSession()->getBuilders('TaskViewModelBuilder') as $builder ) {
    		$builder->build($object);
    	}

		$builder = new TaskModelExtendedBuilder();
		$builder->build($object);

 		return $object;
 	}
 	
 	function getTableDefault()
 	{
		if ( $this->getSessionReportName() == 'mytasks' ) {
			return new WorkItemTable($this->getObject());
		}
		else {
			return new TaskTable($this->getObject());
		}
 	}
 	
 	function getTable() 
 	{
        $method = new ViewTaskListWebMethod();

        $method->setFilter('iteration');
    	
		switch ( $method->getValue() )
		{
   		    case 'chart':
         		        
		        if ( $_REQUEST['report'] == '' )
		        {
					if ( $_REQUEST['pmreportcategory'] == '' ) $_REQUEST['pmreportcategory'] = 'tasks';
	
					return new ReportTable(getFactory()->getObject('PMReport'));
				}
				else
				{
					return $this->getTableDefault();
				}

    		default:
					return $this->getTableDefault();
 		}
 	}

	function needDisplayForm()
	{
		return in_array($_REQUEST['mode'], array('bulk','group')) || parent::needDisplayForm();
	}
	
	function getBulkForm()
	{
		return new TaskBulkForm($this->getObject());
	}
	
 	function getForm() 
 	{
 		return new TaskForm($this->getObject());
 	}

	function getSessionReportName()
	{
		$report_it = getFactory()->getObject('PMReport')->getExact($_REQUEST['report']);
		if ( is_numeric($report_it->getId()) ) {
			return $report_it->get('Report');
		}
		return $report_it->getId();
	}

	function getPageWidgets()
	{
		return array('tasksboard');
	}
}