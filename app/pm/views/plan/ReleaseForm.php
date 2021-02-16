<?php
include_once SERVER_ROOT_PATH."pm/classes/plan/StageMetricsModelBuilder.php";
include_once SERVER_ROOT_PATH.'pm/views/tasks/FieldIssueTraces.php';

class ReleaseForm extends PMPageForm
{
    private $realValues = array();

	function __construct() {
		parent::__construct(getFactory()->getObject('pm_Version'));
	}

	function extendModel()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
		{
			$text = str_replace('%2',
				getSession()->getApplicationUrl().'project/methodology', text(1284));

			$text = str_replace('%1',
				$methodology_it->getReleaseDuration().' '.
				getLanguage()->getWeeksWording($methodology_it->getReleaseDuration()), $text);

			$this->getObject()->setAttributeDescription('FinishDate', $text);
		}

		foreach( array('Project','OrderNum') as $attribute ) {
			$this->getObject()->setAttributeVisible($attribute, false);
		}
		foreach( array('StartDate') as $attribute ) {
			$this->getObject()->setAttributeVisible($attribute, true);
		}

		if ( is_object($this->getObjectIt()) ) {
			foreach( array('Issues', 'Tasks', 'Artefacts', 'IsClosed') as $attribute ) {
				$this->getObject()->setAttributeVisible($attribute, true);
			}
		}

		parent::extendModel();

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( is_object($this->getObjectIt()) && !$this->getObjectIt()->IsFuture() )
		{
		    $builder = new StageMetricsModelBuilder($methodology_it);
            $builder->build($this->getObject());

            $strategy = $methodology_it->getEstimationStrategy();
            list( $capacity, $maximum, $actual_velocity, $estimation ) = $this->getObjectIt()->getRealBurndownMetrics();

            $this->realValues['LeftDuration'] = $capacity;
            $this->realValues['LeftVolume'] = $strategy->getDimensionText(round($estimation, 1));
            $this->realValues['Capacity'] = $strategy->getDimensionText(round($capacity * $actual_velocity, 1));
		}

        $this->getObject()->setAttributeOrderNum('IsClosed', 1000);
	}

	function createField( $attr )
	{
		$field = parent::createField( $attr );
		
		switch ( $attr )
		{
			case 'FinishDate':
				$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
				if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() ) {
					$field->setReadonly( true );
				}
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
                if ( !is_object($this->getObjectIt()) ) return null;
                $boardIt = getFactory()->getObject('Module')->getExact('issues-board');
                return new FieldListOfReferences(
                    $this->getObjectIt()->getRef($attr),
                    array(
                        $boardIt->getDisplayName() => $boardIt->getUrl('release='.$this->getObjectIt()->getId())
                    )
                );

			case 'Tasks':
				if ( !is_object($this->getObjectIt()) ) return null;
                $boardIt = getFactory()->getObject('Module')->getExact('tasks-board');
                $effortsIt = getFactory()->getObject('PMReport')->getExact('tasksefforts');
				return new FieldListOfReferences(
                    $this->getObjectIt()->getRef($attr),
                    array(
                        $boardIt->getDisplayName() => $boardIt->getUrl('issue-release='.$this->getObjectIt()->getId()),
                        $effortsIt->getDisplayName() => $effortsIt->getUrl('iteration='.$this->getObjectIt()->getId())
                    )
                );

			default:
				return parent::createFieldObject($attr);
		}
	}

	function drawScripts()
	{
		parent::drawScripts();
		
		$locale = getSession()->getLanguage()->getLocaleFormatter();
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		if ( !$methodology_it->HasPlanning() && $methodology_it->getReleaseDuration() )
		{
		?>
		<script type="text/javascript">
			$().ready( function() {
				$('#modal-form input[name=StartDate]').change( function() {
					<? if ( !$methodology_it->HasFixedRelease() ) { ?>
					if ( $('#modal-form input[name=FinishDate]').val() != '' ) return;
					<? } ?>
					var start = Date.parse($(this).val());
					var finish = start.add({days: <?=($methodology_it->getReleaseDuration() * 7 - 1)?>});
					$('#modal-form input[name=FinishDate]').val(finish.toString('<?=$locale->getDateJSFormat()?>'));
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
			case 'ActualVelocity':
                $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
                $strategy = $methodology_it->getEstimationStrategy();
				return $strategy->getReleaseVelocityText($this->getObjectIt());

            case 'Capacity':
            case 'LeftDuration':
            case 'LeftVolume':
                return $this->realValues[$attribute];

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
		    	if ( is_numeric($parts[count($parts)-1]) ) {
		    		$parts[count($parts)-1] += 1;
		    		return join('.', $parts);
		    	}
		    	else {
		    		return "";
		    	}
		    default:
		    	return parent::getFieldValue( $attribute );
		}
	}
	
	function getFieldDescription( $name )
	{
		switch ( $name )
		{
			case 'InitialVelocity':
				$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
				$strategy = $methodology_it->getEstimationStrategy();
				$dimension = str_replace('%1', $strategy->getDimensionText(''), array_pop(preg_split('/:/',$strategy->getVelocityText($this->getObject()))));

				if ( $methodology_it->HasPlanning() )
				{
					list($average, $velocity) = getFactory()->getObject('Iteration')->getVelocitySuggested();
					list($releaseAverage, $velocity) = $this->getObject()->getVelocitySuggested();
					$title = str_replace( '%1', $dimension, text(1028));
					if ( $average > 0 ) {
						$title .= '<br/>'.str_replace( '%2', round($velocity, 1),
								str_replace( '%1', round($average, 1), text(2295)));
					}
				}
				else
				{
					list($average, $velocity) = $this->getObject()->getVelocitySuggested();
					$title = str_replace( '%1', $dimension, text(1029));
					if ( $average > 0 ) {
						$title .= '<br/>'.str_replace( '%2', round($velocity, 1),
							str_replace( '%1', round($average, 1), text(2294)));
					}
				}
				return $title;

			case 'FinishDate':
				$object_it = $this->getObjectIt();
				if ( is_object($object_it) ) {
					$offset = $object_it->getFinishOffsetDays();
					if ( $offset > 0 ) {
						return str_replace('%1', $object_it->getDateFormatted('EstimatedFinishDate'),
							str_replace('%2', $offset,
								$object_it->IsFinished() ? text(2293) : text(2302) ));
					}
				}
				return parent::getFieldDescription( $name );

			default:
				return parent::getFieldDescription( $name );
		}
	}

    function getShortAttributes()
    {
        return array_merge(
            parent::getShortAttributes(),
            array(
                'StartDate', 'FinishDate'
            )
        );
    }
}
 