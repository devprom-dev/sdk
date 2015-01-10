<?php

include "TaskResultDictionary.php";
include_once "FieldTaskTypeDictionary.php";
include_once SERVER_ROOT_PATH.'pm/views/time/FieldSpentTimeTask.php';
include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/WorkflowTransitionTaskModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskTrace.php";

class TaskForm extends PMPageForm
{
 	var $request_it;
 	
 	private $move_iteration_methods = array();
 	
    protected function extendModel()
    {
    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));
    	
		$this->getObject()->addPersister( new WatchersPersister() );
		
		parent::extendModel();

		$transition_it = $this->getTransitionIt();
		if ( $transition_it->getId() > 0 )
		{
			$builder = new WorkflowTransitionTaskModelBuilder($transition_it);
			$builder->build( $this->getObject() );
		}
		
		$this->buildMethods();
    }
	
	public function buildMethods()
	{
		$project_roles = getSession()->getRoles();
		$project_it = getSession()->getProjectIt();
		
		if( $project_roles['lead'] && !$project_it->IsPortfolio() && $project_it->getMethodologyIt()->HasPlanning() ) 
		{
			$release_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
						array (
								new IterationTimelinePredicate('not-passed'),
								new FilterBaseVpdPredicate()
						)
				);
			
			while( !$release_it->end() )
			{
				$method = new MoveTaskWebMethod($release_it->copy());
				$method->setRedirectUrl('donothing');
				
				$this->move_iteration_methods[$release_it->getId()] = $method; 
						   			
				$release_it->moveNext();
			}
		}
	}
    
 	function IsAttributeVisible( $attr_name ) 
	{
		$this->object_it = $this->getObjectIt();

		switch ( $attr_name )
		{
			case 'LeftWork':
				if ( !is_object($this->object_it) )
				{
					return false;
				}
				break;
			
			case 'ChangeRequest':
				$hide = is_object($this->object_it) && 
					$this->object_it->get('ChangeRequest') < 1 && 
						$this->getAction() == 'view';
				
				if ( $hide )
				{
					return false;
				}
				break;
		}

		return parent::IsAttributeVisible( $attr_name );
	}
	
	function getTransitionAttributes()
	{
		$fields = array();

		if ( $this->getFieldValue( 'Caption' ) )
		{
		    $fields[] = 'Caption';
		}
		
		$target_it = $this->getTransitionIt()->getRef('TargetState');

		if ( $target_it->get('IsTerminal') == 'Y' )
		{
			$fields[] = 'Result';
		}
		
		return $fields;
	}

	function getNewObjectAttributes()
	{
		return array('Caption', 'Priority', 'Planned', 'Assignee', 'Release', 'TaskType', 'ChangeRequest', 'Attachment', 'OrderNum');
	}
	
	function createFieldObject( $name ) 
	{
		global $_REQUEST, $model_factory;
		
		$this->object_it = $this->getObjectIt();

		$object_it_for_trace = $this->object_it;  

		switch ( $name )
		{
			case 'TestExecution':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceTestExecution') );

			case 'HelpPage':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceHelpPage') );

			case 'TestScenario':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceTestScenario') );

			case 'Requirement':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceRequirement') );

			case 'TraceTask':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceTask') );

			case 'SourceCode':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceSourceCode') );
				
			case 'Fact':
				return new FieldSpentTimeTask( $this->object_it );
				
			case 'Watchers':
			    return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );
			    
			case 'TaskType':
				$tasktype = $model_factory->getObject('TaskType');

				$tasktype->addFilter( new FilterBaseVpdPredicate() );
				
				return new FieldTaskTypeDictionary( $tasktype );

			case 'Assignee':
				$object = getFactory()->getObject('Participant');

	    		$object->addFilter( new ParticipantWorkerPredicate() );
	    		$object->addFilter( new FilterBaseVpdPredicate() );

				return new FieldParticipantDictionary( $object );
				
			case 'Release':
				
				if ( !is_object($this->getObjectIt()) )
				{
					// filter not-passed only when creating a new task
					$iteration = $model_factory->getObject('Iteration');
					
					$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
					
					return new FieldDictionary( $iteration );
				}
				else
				{
					return parent::createFieldObject( $name );
				}

			case 'Result':
				
   				return new TaskResultDictionary( 
   						getFactory()->getObject('TaskType'),
   						is_object($this->getObjectIt()) ? $this->getObjectIt()->get('TaskType') : ''
   				); 
				
			case 'ResultArtefact':
				return new FieldDictionary( $model_factory->getObject('pm_Artefact') );
				
			case 'Attachment':
				return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );
				
			default:
				return parent::createFieldObject( $name );
		}
	}
	
	function createField( $attr )
	{
		global $_REQUEST, $model_factory;
		
		$field = parent::createField( $attr );
		
		$object_it = $this->getObjectIt();
		
    	if ( $_REQUEST['Transition'] > 0 )
    	{
    		switch ( $attr )
    		{
    			case 'Caption':
    				$field->setReadonly( true );
    				break;
    		}
    	} 
		
    	switch ( $attr )
		{
			case 'Release':
				if ( is_object($object_it) && $object_it->getId() > 0 ) return $field;
				
				$object = $this->getObject();
				
				$value = $object->getDefaultAttributeValue( $attr );
				
				if ( $value != '' ) return $field;
				
				$iteration = $field->getObject();
				
				$iteration_it = $iteration->getFirst();
				
				$field->setValue( $iteration_it->getId() );
				
				return $field; 
			
			case 'ChangeRequest':
				$field->setDefault($this->getDefaultValue($attr));
				
				return $field;
				    		
			default:
				return $field;
		}
	}
	
	function getFieldValue( $attr )
	{
		switch( $attr )
		{
		    case 'TaskType':
		    	
		    	if ( $this->getMode() == 'new' )
		    	{
			    	return getFactory()->getObject('TaskType')->getRegistry()->Query(
					    		array (
					    				new FilterBaseVpdPredicate(),
					    				new FilterAttributePredicate('IsDefault', 'Y')
					    		)
					    )->getId();
		    	}
		    	break;
		    	
		    case 'Caption':
		    case 'Priority':
		    	
		    	if ( $this->getMode() == 'new' && parent::getFieldValue('ChangeRequest') != '' )
		    	{
		    		$request_it = getFactory()->getObject('Request')->getExact(parent::getFieldValue('ChangeRequest'));
		    		return $request_it->getHtmlDecoded($attr);
		    	}
		    	
		    	break;
		}
		return parent::getFieldValue( $attr );
	}
	
	function getActions()
	{
		global $model_factory;
		
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( !is_object($object_it) ) return $actions;
		
		$method = new WatchWebMethod( $object_it );
		
		$method->setRedirectUrl('donothing');
		
		if ( $method->hasAccess() )
		{
		    if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
		    
			$actions[] = array( 
			        'name' => $method->getCaption(),
				    'url' => $method->getJSCall() 
			);
		}
		
		return $actions;
	}
	
	function getTransitionActions($object_it)
	{
		$actions = parent::getTransitionActions($object_it);
		
		if( !$object_it->IsFinished() ) 
		{
			$move_actions = array();
			
			foreach( $this->move_iteration_methods as $iteration_id => $method )
			{
				if ( $iteration_id == $object_it->get('Release') ) continue;
				
				$move_actions[] = array(
						'name' => $method->getCaption(), 
						'url' => $method->getJSCall( 
										array( 
												'Task' => $object_it->getId(),
									   			'Release' => $iteration_id
										)
								 )
				);
			}
			
			if ( count($move_actions) > 0 )
			{
				$actions = array_merge( $actions, array(array()), $move_actions);
			}
		}
				
		return $actions;
	}
	
	function getDiscriminatorField()
 	{
 		return $this->getEditMode() ? 'TaskType' : '';
 	}
	
 	function getDiscriminator()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$object_it = $this->getObjectIt();
 		if ( is_object($object_it) )
 		{
 			$ref_it = $object_it->getRef('TaskType');
 			return $ref_it->get('ReferenceName');
 		}
 		elseif ( $_REQUEST['TaskType'] > 0 )
 		{
 			$object = $this->getObject();
 			
 			$ref = $object->getAttributeObject('TaskType');
 			$ref_it = $ref->getExact($_REQUEST['TaskType']);
 			
 			return $ref_it->get('ReferenceName');
 		}
 	}
}