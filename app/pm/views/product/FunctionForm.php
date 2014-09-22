<?php

include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/product/FieldFunctionTrace.php";

include "FieldFeatureTagTrace.php";

class FunctionForm extends PMPageForm
{
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 		    case 'OrderNum':
 		    case 'FinishDate':
 		    case 'ResponsibleAnalyst':
 		    case 'ResponsibleDesigner':
 		    case 'ResponsibleDeveloper':
 		    case 'ResponsibleTester':
 		    case 'ResponsibleDocumenter':
 		        return false;
 		        
 		    case 'Tags':
 		        return true;
 		        
 		    default:
 		        return parent::IsAttributeVisible( $attr_name );
 		}
	}

	function draw()
	{
		global $_REQUEST;
		
		parent::draw();

		$this->object_it = $this->getObjectIt();
		
		if ( isset($this->object_it) && $_REQUEST['formonly'] != 'true')
		{
			echo '<div class="line">';
			echo '</div>';
			echo '<div class="line">';
			echo '</div>';

			echo '<div class="line">';
				echo translate('Комментарии участников');
			echo '</div>';

			echo '<div class="line">';
				$comment_form = new CommentList( $this->object_it );
				$comment_form->draw();
			echo '</div>';
		}
	}

	function createFieldObject( $name )
	{
		global $model_factory;
		
		switch ( $name ) 
		{
			case 'ResponsibleAnalyst':
			case 'ResponsibleDesigner':
			case 'ResponsibleDeveloper':
			case 'ResponsibleTester':
			case 'ResponsibleDocumenter':
				return new FieldParticipantDictionary( 
					$model_factory->getObject('pm_Participant') );

			case 'Requirement':
				return new FieldFunctionTrace( $this->object_it, 
					$model_factory->getObject('FunctionTraceRequirement') );

			case 'Description':
				$field = parent::createFieldObject( $name );
				
				$field->setRows( 8 );
				
				return $field;
		
			case 'Tags':
			    
			    return new FieldFeatureTagTrace( 
			        is_object($this->object_it) ? $this->object_it : null 
			    );
			    
			default:
				return parent::createFieldObject( $name );
		}
	}
}