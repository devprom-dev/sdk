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
		    	
		    case 'StartDate':
		    	$predicates = array( new FilterVpdPredicate() );
		    		
		    	$release = parent::getDefaultValue('Version');
		    	if ( $release != '' ) {
		    			$predicates[] = new FilterAttributePredicate('Version', $release);
		    	}
		    	
	 			$iteration = getFactory()->getObject('Iteration');
	 			foreach( $predicates as $predicate ) {
	 				$iteration->addFilter($predicate);
	 			}
	 			
	 			$aggregate = new \AggregateBase( 'Project', 'FinishDate', 'MAX' );
				$iteration->addAggregate($aggregate);
				$last_date = $iteration->getAggregated()->get($aggregate->getAggregateAlias());
				
				if ( $last_date != '' ) return date('Y-m-j', strtotime('1 day', strtotime($last_date)));
				
				$predicates = array (
						new SortAttributeClause('StartDate.D'),
						new FilterVpdPredicate()
				);
				if ( $release != '' ) {
		    			$predicates[] = new FilterInPredicate($release);
		    	}
	 			$release_it = getFactory()->getObject('Release')->getRegistry()->Query($predicates);
	 			if ( $release_it->getId() != '' ) return $release_it->get('StartDate');

	 			return SystemDateTime::date('Y-m-j');
		}
		
		return $value;
	}
	
	function createField( $attr )
	{
		$field = parent::createField( $attr );
		
		switch ( $attr )
		{
			case 'FinishDate':
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease() ) $field->setReadonly( true );
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