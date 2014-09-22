<?php

include_once SERVER_ROOT_PATH.'pm/views/issues/FieldIssueInverseTrace.php';

include "FieldQuestionTagTrace.php";

class QuestionForm extends PMPageForm
{
 	function IsNeedButtonNew() {
		return false;
	}
	
 	function IsNeedButtonCopy() {
		return false;
	}

	function getTransitionAttributes()
	{
		return array(
			'Content', 'Owner'
		);
	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch($attr_name) 
 		{
 			case 'OrderNum':
 			case 'Comments':
 				return false;

 			case 'Author':
				$object_it = $this->getObjectIt();
 				return isset($object_it) && $this->getAction() != 'show';

 			case 'Tags':
 			    return true;
 				    	
 			default:
				return parent::IsAttributeVisible( $attr_name );
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