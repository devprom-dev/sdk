<?php

use Devprom\ProjectBundle\Service\Task\TaskDefaultsService;

include_once SERVER_ROOT_PATH."pm/classes/issues/validators/ModelValidatorIssueTasks.php";
include_once "FormTaskEmbedded.php";

class FieldTask extends Field
{
 	private $request_it;
 	
 	function __construct( $request_it )
 	{
 		$this->request_it = $request_it;
 		parent::__construct();
 	}
 	
 	function getValidator()
 	{
 		return new ModelValidatorIssueTasks();
 	}
 	
 	function draw( $view = null )
 	{
 		$taskboxes = 0;
 		
        $task = getFactory()->getObject( 'pm_Task' );
        $builder = new TaskModelExtendedBuilder();
        $builder->build($task);

        $filledTypes = array();

		$task_it = $this->request_it->getRef('OpenTasks');
		if ( $task_it->count() > 0 ) {
			$this->drawOpen( $task_it, $taskboxes );
            $filledTypes = array_merge($filledTypes, $task_it->fieldToArray('TaskType'));
		}

        $this->drawByTypes( $task, $taskboxes, $filledTypes );

		$parms = array (
            'Priority' => $this->request_it->get('Priority'),
            'FormActive' => 'Y'
		);
		if ( $taskboxes < 1 ) {
            $this->drawForm( $task, $taskboxes, '', $parms );
        }

        $parms = array (
            'Priority' => $this->request_it->get('Priority'),
            'FormActive' => 'N'
        );
		for ( $i = $taskboxes; $i < 8; $i++ ) {
			$this->drawForm( $task, $taskboxes, 'display:none;', $parms );
			$taskboxes++;
		}

		echo '<div style="float:left;padding-bottom:8px;">';
			echo '<a id="btn-more-tasks" class="btn btn-success btn-sm" onclick="taskboxShow();"><i class="icon-plus icon-white"></i> ' .translate('Еще задачу').'</a>';
		echo '</div>';

		echo '<div class="clearfix">&nbsp;</div>';
		
		$checked = in_array(getSession()->getProjectIt()->get('Tools'), array('sdlc_ru.xml', 'sdlc_en.xml')) ? 'checked' : '';
		
		echo '<label class="checkbox">';
			echo '<input name="'.$this->getName().'" type="hidden" value="tasks">';
		    echo '<input name="dependencies" type="checkbox" class="checkbox" '.$checked.'> '.text(1047);
		echo '</label>';
 	}
 	
  	function drawOpen( & $task_it, & $taskboxes )
 	{
 		$attributes = $task_it->object->getAttributes();
 		
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
 	}
 	
 	function drawByTypes( $task, & $taskboxes, $skipTypes )
 	{
		$target_it = getFactory()->getObject('Transition')
            ->getExact($_REQUEST['Transition'])->getRef('TargetState');

 		$filters = array (
 				new FilterBaseVpdPredicate(),
				new TaskTypeStateRelatedPredicate($target_it->get('ReferenceName'), true),
				new SortOrderedClause()
 		);

		$tasktype_it = getFactory()->getObject('pm_TaskType')->getRegistry()->Query($filters);

 		$parms = array (
 			'Priority' => $this->request_it->get('Priority'),
            'ChangeRequest' => $this->request_it->getId()
 		);

 		while ( !$tasktype_it->end() )
 		{
 			if ( in_array($tasktype_it->getId(), $skipTypes) || $tasktype_it->get('ReferenceName') == 'support' ) {
 				$tasktype_it->moveNext();
 				continue;
 			}
 			
 			$parms['TaskType'] = $tasktype_it->getId();
 			$parms['Assignee'] = TaskDefaultsService::getAssignee($tasktype_it->getId());
	 		
			$this->drawForm( $task, $taskboxes, 'display:block;', $parms );

			$taskboxes++;
	 		$tasktype_it->moveNext();
 		}
 	}
 	
 	function drawForm( $ref, $taskbox, $style = '', $parms )
 	{
	 	$form = new FormTaskEmbedded( is_a($ref, 'Task') ? $ref : $ref->object,
            'ChangeRequest', '', $parms );

 		$form->setSingleton( true );
 		$form->setReadonly( false );
 		
 		if ( is_a($ref, 'TaskIterator') ) $form->setObjectIt($ref);
 		
 		$form->setTabIndex( $this->getTabIndex() );
 		 
 		echo '<div id="tb'.$form->getFormId().'" form-id="'.$form->getFormId().'" class="taskbox span4" style="'.$style.'">';
	 		echo '<span class="input-block-level well well-text">'; 		
		 		$form->draw();
			 	if ( !is_a($ref, 'TaskIterator') ) {
			 		echo '<i class="icon-remove"></i> <a class="dashed" onclick="taskboxClose(' .$form->getFormId().');">'.translate('Скрыть').'</a>';
			 	}
			echo '</span>';
		echo '</div>';
 	}
}