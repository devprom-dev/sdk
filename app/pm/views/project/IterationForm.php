<?php

include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDatesCausality.php";

class IterationForm extends PMPageForm
{
	function __construct() {
		parent::__construct(getFactory()->getObject('Iteration'));
	}

	function extendModel()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasFixedRelease() )
		{
			$text = str_replace('%2',
				getSession()->getApplicationUrl().'project/methodology', text(1283));

			$text = str_replace('%1',
				$methodology_it->getReleaseDuration().' '.
				getLanguage()->getWeeksWording($methodology_it->getReleaseDuration()), $text);

			$this->getObject()->setAttributeDescription('FinishDate', $text);
		}

		$stages_num = getFactory()->getObject('pm_ProjectStage')->getRegistry()->Count(
				array (
					new FilterVpdPredicate()
				)
		);
		if ( $stages_num < 1 ) {
			$this->getObject()->setAttributeVisible('ProjectStage', false);
		}

		if ( is_object($this->getObjectIt()) ) {
			foreach( array('Issues', 'Tasks') as $attribute ) {
				$this->getObject()->setAttributeVisible($attribute, true);
			}
		}

		parent::extendModel();
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
		    	if ( $value == '' && getSession()->getProjectIt()->getMethodologyIt()->HasReleases() )
		    	{
		    		return getFactory()->getObject('Release')->getRegistry()->Query(
			    				array (
			    						new FilterVpdPredicate(),
			    						new ReleaseTimelinePredicate('current')
			    				)
		    			)->getId();
		    	}
		    	break;

			case 'InitialVelocity':
				$releaseId = $this->getDefaultValue('Version');
				if ( $releaseId != '' ) {
					return round(getFactory()->getObject('Release')->getExact($releaseId)->getVelocity(),0);
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
				
				if ( $last_date != '' ) return date('Y-m-j', strtotime('1 weekday', strtotime($last_date)));
				
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

	function getFieldDescription( $name )
	{
		switch ( $name )
		{
			case 'InitialVelocity':
				list($average, $velocity) = $this->getObject()->getVelocitySuggested();
				return str_replace( '%2', round($velocity, 1),
							str_replace( '%1', round($average, 1), text(2125)));

			default:
				return parent::getFieldDescription( $name );
		}
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

	function createFieldObject($attr)
	{
		switch ( $attr )
		{
			case 'Issues':
			case 'Tasks':
				if ( !is_object($this->getObjectIt()) ) return null;
				return new FieldListOfReferences( $this->getObjectIt()->getRef($attr) );

			default:
				return parent::createFieldObject($attr);
		}
	}

	function drawScripts()
	{
		parent::drawScripts();
		
		$locale = getSession()->getLanguage()->getLocaleFormatter();
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->getReleaseDuration() > 0 )
		{
		?>
		<script type="text/javascript">
			$().ready( function() {
				$('input[name=StartDate]').change( function() {
					<? if ( !$methodology_it->HasFixedRelease() ) { ?>
					if ( $('input[name=FinishDate]').val() != '' ) return;
					<? } ?>
					var start = Date.parse($(this).val());
					var finish = start.add({days: <?=($methodology_it->getReleaseDuration() * 7 - 1)?>});
					$('input[name=FinishDate]').val(finish.toString('<?=$locale->getDateJSFormat()?>'));
				}).trigger('change');
			});
		</script>
		<?php
		}
	}
} 