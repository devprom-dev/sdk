<?php
include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDatesCausality.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/StageMetricsModelBuilder.php";
include_once SERVER_ROOT_PATH.'pm/views/tasks/FieldIssueTraces.php';
include_once "fields/FieldVelocity.php";

class IterationForm extends PMPageForm
{
    private $realValues = array();
    private $estimationStrategy = null;

	function __construct() {
		parent::__construct(getFactory()->getObject('Iteration'));
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        $this->estimationStrategy = $methodology_it->getIterationEstimationStrategy();
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

		if ( is_object($this->getObjectIt()) ) {
			foreach( array('Issues', 'Tasks', 'Artefacts', 'IsClosed') as $attribute ) {
				$this->getObject()->setAttributeVisible($attribute, true);
			}
        }

		parent::extendModel();

        if ( is_object($this->getObjectIt()) && !$this->getObjectIt()->IsFuture() )
        {
            $builder = new StageMetricsModelBuilder($methodology_it);
            $builder->build($this->getObject());

            list( $capacity, $maximum, $actual_velocity, $estimation ) = $this->getObjectIt()->getRealBurndownMetrics();

            $this->realValues['LeftDuration'] = $capacity;
            $this->realValues['LeftVolume'] = $this->estimationStrategy->getDimensionText(round($estimation, 1));
            $this->realValues['Capacity'] = $this->estimationStrategy->getDimensionText(round($capacity * $actual_velocity, 1));
        }

		$this->getObject()->setAttributeOrderNum('Project', 80);
        $this->getObject()->setAttributeOrderNum('Version', 30);
        $this->getObject()->setAttributeOrderNum('IsClosed', 1000);
	}

 	function IsAttributeVisible( $attr_name )
 	{
 		switch ( $attr_name )
 		{
 			case 'OrderNum':
 				return false;
 			default:
				return parent::IsAttributeVisible( $attr_name );
 		}
	}

	function getFieldValue( $attribute )
	{
		switch($attribute)
        {
			case 'ActualVelocity':
                return $this->estimationStrategy->getReleaseVelocityText($this->getObjectIt());

            case 'Capacity':
            case 'LeftDuration':
            case 'LeftVolume':
                return $this->realValues[$attribute];

			default:
				return parent::getFieldValue($attribute);
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
				
				if ( $last_date != '' ) {
				    return max(SystemDateTime::date('Y-m-j'), date('Y-m-j', strtotime('1 weekday', strtotime($last_date))));
                }
				
				$predicates = array (
                    new SortAttributeClause('StartDate.D'),
                    new FilterVpdPredicate()
				);
				if ( $release != '' ) {
		    			$predicates[] = new FilterInPredicate($release);
		    	}
	 			$release_it = getFactory()->getObject('Release')->getRegistry()->Query($predicates);
	 			if ( $release_it->getId() != '' ) {
	 			    return max(SystemDateTime::date('Y-m-j'), $release_it->get('StartDate'));
                }

	 			return SystemDateTime::date('Y-m-j');
		}
		
		return $value;
	}

	function getFieldDescription( $name )
	{
		switch ( $name )
		{
			case 'InitialVelocity':
			    if ( !$this->getEditMode() ) return '';

				$dimension = str_replace('%1',
                    $this->estimationStrategy->getDimensionText(''),
                        array_pop(preg_split('/:/',$this->estimationStrategy->getVelocityText($this->getObject()))));

				$title = str_replace( '%1', $dimension, text(2125));
				list($average, $velocity) = $this->getObject()->getVelocitySuggested();
				if ( $average > 0 ) {
					$title .= '<br/>'.str_replace( '%2', round($velocity, 1),
							str_replace( '%1', round($average, 1), text(2296)));
				}
				return $title;

			case 'FinishDate':
				$object_it = $this->getObjectIt();
				if ( is_object($object_it) ) {
					$offset = $object_it->getFinishOffsetDays();
					if ( $offset > 0 ) {
						return str_replace('%1', $object_it->getDateFormatted('EstimatedFinishDate'),
									str_replace('%2', $offset,
										$object_it->IsFinished() ? text(2293) : text(2302)));
					}
				}
				return parent::getFieldDescription( $name );

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
            case 'Artefacts':
                return new FieldIssueTraces(is_object($this->object_it) ? $this->object_it->get($attr) : '');

			case 'Issues':
            case 'Increments':
                if ( !is_object($this->getObjectIt()) ) return null;
                if ( $this->getObjectIt()->get($attr) == '' ) return null;

                if ( $attr == 'Increments' || !getSession()->IsRDD() ) {
                    $boardIt = getFactory()->getObject('Module')->getExact('issues-board');
                }
                else {
                    $boardIt = getFactory()->getObject('Module')->getExact('requirements/issuesboard');
                }
                return new FieldListOfReferences(
                    $this->getObject()->getAttributeObject($attr)->getRegistry()->Query(
                        array (
                            new FilterInPredicate($this->getObjectIt()->get($attr)),
                            new SortAttributeClause('State')
                        )
                    ),
                    array(
                        $boardIt->getDisplayName() => $boardIt->getUrl('iteration='.$this->getObjectIt()->getId())
                    )
                );
			case 'Tasks':
				if ( !is_object($this->getObjectIt()) ) return null;
				if ( $this->getObjectIt()->get($attr) == '' ) return null;
                $boardIt = getFactory()->getObject('Module')->getExact('tasks-board');
                $effortsIt = getFactory()->getObject('PMReport')->getExact('tasksefforts');
				return new FieldListOfReferences(
					$this->getObject()->getAttributeObject($attr)->getRegistry()->Query(
						array (
							new FilterInPredicate($this->getObjectIt()->get($attr)),
							new SortAttributeClause('State')
						)
					),
                    array(
                        $boardIt->getDisplayName() => $boardIt->getUrl('iteration='.$this->getObjectIt()->getId()),
                        $effortsIt->getDisplayName() => $effortsIt->getUrl('iteration='.$this->getObjectIt()->getId())
                    )
				);
            case 'InitialVelocity':
                return new FieldVelocity($this->estimationStrategy);

			default:
				return parent::createFieldObject($attr);
		}
	}

	function getShortAttributes()
    {
        return array_merge(
            array_filter(parent::getShortAttributes(), function( $value ) {
                return $value != 'Project';
            }),
            array(
                'StartDate', 'FinishDate', 'Version'
            )
        );
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
			$(document).ready( function() {
			    setTimeout(function() {
                    $('#modal-form input[name=StartDate]').change( function() {
                        <? if ( !$methodology_it->HasFixedRelease() ) { ?>
                        if ( $('#modal-form input[name=FinishDate]').val() != '' ) return;
                        <? } ?>
                        var start = Date.parse($(this).val());
                        var finish = start.add({days: <?=($methodology_it->getReleaseDuration() * 7 - 1)?>});
                        $('#modal-form input[name=FinishDate]').val(finish.toString('<?=$locale->getDateJSFormat()?>'));
                    }).trigger('change');
                }, 1000);
			});
		</script>
		<?php
		}
	}
} 