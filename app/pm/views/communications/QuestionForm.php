<?php

include_once SERVER_ROOT_PATH.'pm/views/issues/FieldIssueInverseTrace.php';

include "FieldQuestionTagTrace.php";

class QuestionForm extends PMPageForm
{
    protected function extendModel()
    {
    	$this->getObject()->setAttributeVisible('Comments', false);
    	$this->getObject()->setAttributeVisible('OrderNum', false);
    	$this->getObject()->setAttributeVisible('Tags', true);
   		$this->getObject()->setAttributeVisible('Author', 
   				is_object($this->getObjectIt()) && $this->getAction() != 'show');
   		
   		return parent::extendModel();
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
			    
			default:
				$field = parent::createFieldObject( $name );
				
				switch ( $name )
				{
					case 'Content':
						$field->setRows(10);
						break;
				}
				
				return $field;
		}
	}
	
	function getActions()
	{
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( is_object($object_it) )
		{
			if ( $actions[count($actions) - 1]['name'] != '' ) array_push($actions, array());
			
			$method = new ConvertQuestionWebMethod( $object_it );
			
			array_push($actions, array( 
				'name' => $method->getCaption(), 
				'url' => $method->getJSCall() 
			));
		}
	
		return $actions;	
	}
}