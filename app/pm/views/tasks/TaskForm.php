<?php

include "TaskResultDictionary.php";
include_once "FieldTaskTypeDictionary.php";
include_once SERVER_ROOT_PATH.'pm/views/time/FieldSpentTimeTask.php';
include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskTrace.php";

class TaskForm extends PMPageForm
{
 	var $request_it;
 	
    protected function extendModel()
    {
    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));
    	
    	parent::extendModel();
    	
		$builder = new TaskModelExtendedBuilder();
		
		$builder->build( $this->getObject() );

		$this->getObject()->addPersister( new WatchersPersister() );
    }
	
	function getModelValidator()
	{
		$validator = parent::getModelValidator();

		if ( $this->IsAttributeRequired('Fact') )
		{
			$validator->addValidator( new ModelValidatorEmbeddedForm('Fact', 'Capacity') );
		}
		
		return $validator;
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
	
 	function IsAttributeRequired( $attr_name ) 
	{
		switch ( $attr_name )
		{
			case 'Assignee':
				return !getSession()->getProjectIt()->getMethodologyIt()->IsParticipantsTakesTasks();
				
			default:
				return parent::IsAttributeRequired( $attr_name );
		}
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
	
	function getActions()
	{
		global $model_factory;
		
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( !is_object($object_it) ) return $actions;
		
		$project_roles = getSession()->getRoles();
		
		if( !$object_it->IsFinished() && $project_roles['lead'] && !getSession()->getProjectIt()->IsPortfolio() ) 
		{
			if ( !isset($this->futher_it) )
			{
				$release = $model_factory->getObject('Iteration');
				
				$release->addFilter( new IterationTimelinePredicate('not-passed') );
				
				$this->futher_it = $release->getAll();
			}
			else
			{
				$this->futher_it->moveFirst();
			}
			
			$need_separator = true;
			while( !$this->futher_it->end() )
			{
				if ( $this->futher_it->getId() != $object_it->get('Release') )
				{
					if ( $need_separator )
					{
						array_push($actions, array());
						$need_separator = false;
					}
					
					$it = $this->futher_it->_clone();
					
					$method = new MoveTaskWebMethod($it);
					
					$method->setRedirectUrl('donothing');
		
					array_push($actions,
							   array( 'name' => $method->getCaption(), 
							   		  'url' => $method->getJSCall( array( 'Task' => $object_it->getId(),
							   			'Release' => $this->futher_it->getId())) ) );
				}
						   			
				$this->futher_it->moveNext();
			}
		}
				
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