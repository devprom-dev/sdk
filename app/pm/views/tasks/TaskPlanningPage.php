<?php

include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskModelExtendedBuilder.php";

include "TaskForm.php";
include "TaskBulkForm.php";
include "IterationBurndownSection.php";
include "WorkloadSection.php";
include "TaskBoardTable.php";
include SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";

class TaskPlanningPage extends PMPage
{
 	function TaskPlanningPage()
 	{
 		global $_REQUEST, $model_factory;

 		getSession()->addBuilder( new TaskModelExtendedBuilder() );
 		
 		parent::PMPage();
 		
 		if ( $_REQUEST['view'] == 'chart' ) return;
 		
 		if ( $this->needDisplayForm() )
 		{
 			$object_it = $this->getObjectIt();
 			
 			if ( is_object($object_it) && $object_it->count() > 0 )
 			{
				$form = $this->getFormRef();
			
				if ( $_REQUEST['Transition'] == '' )
				{
 				    $this->addInfoSection( new PageSectionComments($object_it) );
				}
				
 				$this->addInfoSection( new StatableLifecycleSection( $object_it ) );
 				$this->addInfoSection( new PMLastChangesSection ( $object_it ) );
 			}
 		}
 		elseif ( $_REQUEST['mode'] != 'bulk' )
 		{
 		    if ( $_REQUEST['view'] == 'board' ) $this->addInfoSection( new FullScreenSection() );
 			
 		    $report_it = getFactory()->getObject('PMReport')->getExact('iterationburndown');
 			
 			if ( $report_it->getId() > 0 )
 			{
 				$this->addInfoSection( new IterationBurndownSection () );
 			}

 			$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 			
 			if ( $methodology_it->TaskEstimationUsed() )
 			{
 				$this->addInfoSection( new WorkloadSection () );
 			}
 		}
 	}
 	
 	function getObject()
 	{
 		return getFactory()->getObject('Task');
 	}
 	
 	function getTableDefault()
 	{
 	    return new TaskBoardTable($this->getObject());
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
	
 	function getForm() 
 	{
 		switch ( $_REQUEST['mode'] )
 		{
 		    case 'bulk':
 		        return new TaskBulkForm( $this->getObject() );
 		    
 		    default:
				return new TaskForm( $this->getObject() );
 		}
 	}
}