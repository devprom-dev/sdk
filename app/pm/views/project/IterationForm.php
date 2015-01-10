<?php

include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDatesCausality.php";

class IterationForm extends PMPageForm
{
	function __construct() 
	{
		$object = getFactory()->getObject('Iteration');
		
		parent::__construct( $object );
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		if ( $methodology_it->HasFixedRelease() )
		{
    		$text = str_replace('%2', 
    			getSession()->getApplicationUrl().'project/methodology', text(1283));
    		
    		$text = str_replace('%1', 
    			$methodology_it->getReleaseDuration().' '.
    				getLanguage()->getWeeksWording($methodology_it->getReleaseDuration()), $text);
    		
    		$object->setAttributeDescription('FinishDate', $text);
		}
	}
	
	function buildModelValidator()
	{
		$validator = parent::buildModelValidator();

		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease() )
		{
			$validator->addValidator( new ModelValidatorDatesCausality() );
		}
		
		return $validator;
	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'OrderNum':
 			case 'Project':
 			case 'IsCurrent':
 			case 'IsDraft':
 				return false;
 				
 			case 'Version':
 				return true;
 				
 			case 'InitialVelocity':
 				return false;

 			default:
				return parent::IsAttributeVisible( $attr_name );
 		}
	}
	
	function getDefaultValue( $attribute )
	{
		$value = parent::getDefaultValue( $attribute );
		
		switch($attribute)
		{
		    case 'Version':
		    	if ( $value == '' )
		    	{
		    		return getFactory()->getObject('Release')->getRegistry()->Query(
			    				array (
			    						new FilterVpdPredicate(),
			    						new ReleaseTimelinePredicate('current')
			    				)
		    			)->getId();
		    	}
		    	break;
		}
		
		return $value;
	}
	
	function createField( $attr )
	{
		$field = parent::createField( $attr );
		
		switch ( $attr )
		{
			case 'FinishDate':
				
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease() )
				{
					$field->setReadonly( true );
				}
				
				break;
		}
		
		return $field; 
	}
	
	function drawScripts()
	{
		parent::drawScripts();
		
		$locale = getSession()->getLanguage()->getLocaleFormatter();
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		 
		if ( $methodology_it->HasFixedRelease() )
		{
		?>
		<script type="text/javascript">
	
		$().ready( function() {
	
			$('#pm_ReleaseStartDate').change( function() 
			{
				var start = Date.parse($(this).val());
				var finish = start.add({days: <?=($methodology_it->getReleaseDuration() * 7 - 1)?>});
				 
				$('#pm_ReleaseFinishDate').val(finish.toString('<?=$locale->getDateJSFormat()?>'));
			}).trigger('change');
			
		});
		
		</script>
		<?php
		}
	}
} 