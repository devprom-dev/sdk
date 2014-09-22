<?php

include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDatesCausality.php";

class ReleaseForm extends PMPageForm
{
	function __construct() 
	{
		$object = getFactory()->getObject('pm_Version');
		
		parent::__construct( $object );
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
		{
			$text = str_replace('%2', 
				getSession()->getApplicationUrl().'project/methodology', text(1284));
			
			$text = str_replace('%1', 
				$methodology_it->getReleaseDuration().' '.
					getLanguage()->getWeeksWording($methodology_it->getReleaseDuration()), $text);
			
			$object->setAttributeDescription('FinishDate', $text);
		}
		
		if ( !$methodology_it->HasFixedRelease() )
		{
			$this->getModelValidator()->addValidator( new ModelValidatorDatesCausality() );
		}
	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'OrderNum':
 			case 'Project':
 				return false;
 				
 			case 'StartDate':
 				return true;
 				
 			default:
 				return parent::IsAttributeVisible( $attr_name );
 		}
	}

	function createField( $attr )
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$field = parent::createField( $attr );
		
		$session = getSession();
		
		switch ( $attr )
		{
			case 'FinishDate':
				
				if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
				{
					$field->setReadonly( true );
				}
				
				break;
		}
		
		return $field; 
	}
	
	function drawScripts()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		parent::drawScripts();
		
		$locale = getSession()->getLanguage()->getLocaleFormatter();
		
		if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
		{
		?>
		<script type="text/javascript">
	
		$().ready( function() 
		{
			$('#pm_VersionStartDate').change( function() 
			{
				var start = Date.parse($(this).val());
				var finish = start.add({days: <?=($methodology_it->getReleaseDuration() * 7 - 1)?>});
				 
				$('#pm_VersionFinishDate').val(finish.toString('<?=$locale->getDateJSFormat()?>'));
			}).trigger('change');
		});
		
		</script>
		<?php
		}
	}
	
	function getFieldValue( $attribute )
	{
		switch( $attribute )
		{
		    case 'Caption':
		    	
		    	$value = parent::getFieldValue( $attribute );
		    	
		    	if ( $value != '' || is_object($this->getObjectIt()) ) return $value;
		    	
		    	$release_it = $this->getObject()->getRegistry()->Query(
		    			array (
		    					new FilterBaseVpdPredicate(),
		    					new SortAttributeClause('StartDate.D')
		    			)
		    	);
		    	
		    	if ( $release_it->get('Caption') == '' ) return '1';
		    	
		    	$parts = preg_split('/\./', $release_it->get('Caption'));

		    	if ( is_numeric($parts[count($parts)-1]) )
		    	{
		    		$parts[count($parts)-1] += 1;
		    		
		    		return join('.', $parts);
		    	}
		    	else
		    	{
		    		return "";
		    	}
		    	
		    default:
		    	return parent::getFieldValue( $attribute );
		}
	}
	
	function getFieldDescription( $name )
	{
		global $model_factory;
		
		switch ( $name )
		{
			case 'InitialVelocity':
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
				{
					$iteration = $model_factory->getObject('Iteration');
					$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::PAST) );
					$iteration->addSort( new SortAttributeClause('StartDate.D') );
					
					$iteration_it = $iteration->getAll();
					if ( $iteration_it->count() > 2 )
					{ 
						$average = 0;
						for( $i = 0; $i < 3; $i++ ) {
							$average += $iteration_it->getVelocity();
							$iteration_it->moveNext();
						}
						$average = $average / 3;
					}
					else
					{
						$average = 0;
					}
					
					$release = $model_factory->getObject('Release');
					
					$release->addFilter( new ReleaseTimelinePredicate('past') );
					$release->addFilter( new FilterBaseVpdPredicate() );
					
					$release->addSort( new SortAttributeClause('StartDate.D') );
					
					$release_it = $release->getAll();
					
					$velocity = $release_it->getId() > 0 ? $release_it->getVelocity() : 0;
					
					$title = str_replace( '%2', round($velocity, 1), 
						str_replace( '%1', round($average, 1), text(1028)));
				}
				else
				{
					$release = $model_factory->getObject('Release');
					
					$release->addFilter( new ReleaseTimelinePredicate('past') );
					$release->addFilter( new FilterBaseVpdPredicate() );
					
					$release->addSort( new SortAttributeClause('StartDate.D') );
					
					$release_it = $release->getAll();
				
					$velocity = $release_it->getId() > 0 ? $release_it->getVelocity() : 0;
					
					if ( $release_it->count() > 2 )
					{ 
						$average = 0;
						for( $i = 0; $i < 3; $i++ ) {
							$average += $release_it->getVelocity();
							$release_it->moveNext();
						}
						$average = $average / 3;
					}
					else
					{
						$average = 0;
					}
					
					$title = str_replace( '%2', round($velocity, 1), 
						str_replace( '%1', round($average, 1), text(1029)));
				}
				return $title;
				
			default:
				return parent::getFieldDescription( $name );
		}
	}
} 
 