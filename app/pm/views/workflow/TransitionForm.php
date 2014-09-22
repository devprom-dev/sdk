<?php

include "FieldTransitionProjectRole.php";
include "FieldTransitionAttribute.php";
include "FieldTransitionPredicate.php";
include "FieldTransitionResetField.php";

class TransitionForm extends PMPageForm
{
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'ProjectRoles':
 			case 'Attributes':
 			case 'Predicates':
 			case 'ResetFields':
 				return true;

 			default:
 				return parent::IsAttributeVisible( $attr_name );
 		}
 	}
 	
 	function getFieldDescription( $attr )
 	{
 		switch( $attr )
 		{
 			case 'ProjectRoles':
 				return text(893);
 				
 			case 'Attributes':
 				return text(897);
 				
 			case 'ResetFields':
 				return text(1146);
 				
 			case 'Predicates':
 				return text(1141);
 		}
 	}

	function createFieldObject( $attr_name ) 
	{
		global $model_factory, $_REQUEST;

		$object_it = $this->getObjectIt();
		
		$state = $model_factory->getObject('pm_State');
		
		if ( $_REQUEST['SourceState'] > 0 )
		{
			$state_it = $state->getByRef('pm_StateId', $_REQUEST['SourceState']);
		}
		elseif ( is_object($object_it) )
		{
			$state_it = $object_it->getRef('TargetState');
			
			if ( $state_it->getId() == '' )
			{
			    $state_it = $object_it->getRef('SourceState');
			}
		}
		
		switch( $attr_name )
		{
			case 'TargetState': 
				$state->addFilter( new StateClassPredicate($state_it->get('ObjectClass')) );
				$state->addFilter( new FilterBaseVpdPredicate() );
				
				$state->addSort( new SortOrderedClause() );
				
				return new FieldDictionary( $state );
				
			case 'ProjectRoles':
				return new FieldTransitionProjectRole($this->object_it);

			case 'Attributes':
				$field = new FieldTransitionAttribute($this->object_it);

				$field->setStateIt( $state_it );
				
				return $field;
				
			case 'Predicates':
				$field = new FieldTransitionPredicate($this->object_it);
				
				$field->setStateIt( $state_it );
				
				return $field;
				
			case 'ResetFields':
				$field = new FieldTransitionResetField( $this->object_it );
				
				$field->setStateIt( $state_it );
				
				return $field;
				
			default:
				return parent::createFieldObject( $attr_name );
		}
	}
	
	function drawScripts()
	{
		parent::drawScripts();
		
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#pm_TransitionTargetState').change(function() {
					if ( $('#pm_TransitionCaption').val() != '' ) return;
					
					$('#pm_TransitionCaption').val( 
						$(this).find("option:selected")
							.text().replace(/^[\s\n\r]+|[\s\n\r]+$/, '') );
				});
			});
		</script>
		<?php 
	}
}