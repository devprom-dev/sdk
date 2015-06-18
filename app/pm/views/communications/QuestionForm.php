<?php

include_once SERVER_ROOT_PATH.'pm/views/issues/FieldIssueInverseTrace.php';

include "FieldQuestionTagTrace.php";

class QuestionForm extends PMPageForm
{
	private $create_issue_method = null;
	
    protected function extendModel()
    {
    	$this->getObject()->setAttributeVisible('Comments', false);
    	$this->getObject()->setAttributeVisible('OrderNum', false);
    	$this->getObject()->setAttributeVisible('Tags', true);
   		$this->getObject()->setAttributeVisible('Author', 
   				is_object($this->getObjectIt()) && $this->getAction() != 'show');

   		$this->buildMethods();
   		
   		return parent::extendModel();
    }
    
    protected function buildMethods()
    {
   		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_ChangeRequest'));
   		if ( $method->hasAccess() ) {
   			$method->setRedirectUrl('donothing');
   			$this->create_issue_method = $method; 
    	}
    }
    
	function draw()
	{
		echo '<div class="line">';
			parent::draw();
		echo '</div>';

		$object_it = $this->getObjectIt();
		
		if ( isset($object_it) )
		{
			echo '<div style="padding:4px;"><b>';
				echo_lang('Комментарии');
			echo '</b></div>';

			echo '<div style="padding:10px 0 0 4px;">';
				$comment_form = new CommentList( $object_it );
				$comment_form->draw();
			echo '</div>';
		}
	}

	function createFieldObject( $name )
	{
		global $model_factory;
		
		switch ( $name )
		{
			case 'Author':
				return new FieldDictionary( $model_factory->getObject('cms_User') );
				
			case 'TraceRequests':
				return new FieldIssueInverseTrace( $this->getObjectIt(),
					 $model_factory->getObject('RequestInversedTraceQuestion') );

			case 'Tags':
			    
			    return new FieldQuestionTagTrace( 
			        is_object($this->object_it) ? $this->object_it : null 
			    );
			
			case 'Attachment':
				return new FieldAttachments( is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->object );
			    
			case 'Content':
				$field = new FieldLargeText();
				$field->setRows(10);
				return $field;
				
			default:
				return parent::createFieldObject( $name );
		}
	}
	
	function getActions()
	{
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		if ( is_object($object_it) )
		{
			if ( is_object($this->create_issue_method) ) {
				if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
				$actions[] = array( 
					'name' => text(747), 
					'url' => $this->create_issue_method->getJSCall(array('Question' => $object_it->getId())) 
				);
			}
		}
	
		return $actions;	
	}
}