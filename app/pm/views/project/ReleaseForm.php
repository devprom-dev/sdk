<?php
include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDatesCausality.php";

class ReleaseForm extends PMPageForm
{
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

		if ( !$methodology_it->HasPlanning() && $methodology_it->getReleaseDuration() )
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
		switch ( $name )
		{
			case 'InitialVelocity':
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
				{
					list($average, $velocity) = getFactory()->getObject('Iteration')->getVelocitySuggested();
					list($releaseAverage, $velocity) = $this->getObject()->getVelocitySuggested();
					$title = str_replace( '%2', round($velocity, 1),
						str_replace( '%1', round($average, 1), text(1028)));
				}
				else
				{
					list($average, $velocity) = $this->getObject()->getVelocitySuggested();
					$title = str_replace( '%2', round($velocity, 1),
						str_replace( '%1', round($average, 1), text(1029)));
				}
				return $title;
				
			default:
				return parent::getFieldDescription( $name );
		}
	}
} 
 