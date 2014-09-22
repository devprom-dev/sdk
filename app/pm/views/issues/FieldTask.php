<?php

include_once "FormTaskEmbedded.php";
        
class FieldTask extends Field
{
 	var $request_it, $iteration_it;
 	
 	function FieldTask( $request_it, $iteration_it )
 	{
 		$this->request_it = $request_it;
 		$this->iteration_it = $iteration_it;
 		
 		parent::Field();
 	}
 	
 	function draw()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$taskboxes = 0;
 		
 		$transition = $_REQUEST['Transition'];
 		
 		$task = $model_factory->getObject( 'pm_Task' );
		$state_it = $this->request_it->getStateIt();
		
		if ( $state_it->get('ReferenceName') != 'planned' )
		{
			$task_it = $this->request_it->getRef('OpenTasks');
			
			if ( $task_it->count() > 0 )
			{
				$taskboxes = $this->drawOpen( $task_it );
			}
		}
		
		if ( $taskboxes < 1 )
		{
 			$taskboxes = $this->drawByTypes( $task );
		}
 		 
		$parms = array (
				'Priority' => $this->request_it->get('Priority'),
				'FormActive' => 'N'
		);
		
		for ( $i = $taskboxes; $i < 8; $i++ )
		{
			$this->drawForm( $task, $taskboxes, 'display:none;', $parms );
			
			$taskboxes++;		
		}

 		$_REQUEST['Transition'] = $transition;
		
		echo '<div style="float:left;padding-bottom:8px;">';
			echo '<a id="btn-more-tasks" class="btn btn-success btn-small" onclick="javascript: taskboxShow();"><i class="icon-plus icon-white"></i> '.translate('Еще задачу').'</a>';
		echo '</div>';

		echo '<div class="clearfix">&nbsp;</div>';
		
		echo '<label class="checkbox">';
			echo '<input name="'.$this->getName().'" type="hidden" value="tasks">';
		    echo '<input name="dependencies" type="checkbox" class="checkbox"> '.text(1047);
		echo '</label>';
 	}
 	
  	function drawOpen( & $task_it )
 	{
 		global $model_factory;
 		
 		$attributes = $task_it->object->getAttributes();
 		
 		$taskboxes = 0;
 		
 		$parms = array();
 		
 		while ( !$task_it->end() )
 		{
 			foreach ( $attributes as $key => $attribute ) 
 			{
 				if ( $key == 'RecordCreated' ) continue;
 				if ( $key == 'RecordModified' ) continue;
 				if ( $key == 'State' ) continue;
 				if ( $key == 'Release' ) continue;
 				
 				$parms[$key] = $task_it->get_native($key);
 			}
 			
			$this->drawForm( $task_it, $taskboxes, '', $parms );		

			$taskboxes++;
			
	 		$task_it->moveNext();
 		}

 		return $taskboxes;
 	} 	
 	
 	function drawByTypes( $task )
 	{
 		global $model_factory;
 		
 		$tasktype = $model_factory->getObject('pm_TaskType');
 		
		$tasktype->addFilter( new TaskTypePlannablePredicate() );
		
		$tasktype->addFilter( new FilterBaseVpdPredicate() ); 
		
		if ( is_object($this->iteration_it) && $this->iteration_it->get('ProjectStage') != '' )
		{
 			$tasktype->addFilter( new TaskTypeStageRelatedPredicate(
 				$this->iteration_it->get('ProjectStage')) );
		}
		$tasktype_it = $tasktype->getAll();
		
 		$taskboxes = 0;
 		
 		$parms = array (
 				'Priority' => $this->request_it->get('Priority')
 		);
 		
 		while ( !$tasktype_it->end() )
 		{
 			if ( $tasktype_it->get('ReferenceName') == 'support' )
 			{
 				$tasktype_it->moveNext();
 				continue;
 			}
 			
 			$parms['TaskType'] = $tasktype_it->getId();
	 		
			$this->drawForm( $task, $taskboxes, '', $parms );		

			$taskboxes++;
			
	 		$tasktype_it->moveNext();
 		}
 		
 		return $taskboxes;
 	}
 	
 	function drawForm( $ref, $taskbox, $style = '', $parms )
 	{
	 	$form = new FormTaskEmbedded( is_a($ref, 'Task') ? $ref : $ref->object, 'ChangeRequest' );

 		$form->setSingleton( true );

 		$form->setReadonly( false );
 		
 		if ( is_a($ref, 'TaskIterator') ) $form->setObjectIt($ref);
 		
 		$form->setTabIndex( $this->getTabIndex() );
 		 
 		echo '<div id="tb'.$form->getFormId().'" form-id="'.$form->getFormId().'" class="taskbox span4" style="'.$style.'">';
	 		echo '<span class="input-block-level well well-text">'; 		

	 	 		foreach( $parms as $key => $value )
		 		{
		 			$_REQUEST[$form->getFieldName($key)] = $value;
		 		}
	 		
		 		$form->draw();
			 	
			 	if ( !is_a($ref, 'TaskIterator') )
			 	{
			 		echo '<i class="icon-remove"></i> <a class="dashed" onclick="javascript: taskboxClose('.$form->getFormId().');">'.translate('Скрыть').'</a>';
			 	}
			
			echo '</span>';
		echo '</div>';
 	}
}