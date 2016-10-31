<?php

class FieldStateTransitions extends Field
{
	private $state_it = null;
	
	function __construct( $state_it )
	{
		$this->state_it = $state_it instanceof OrderedIterator ? $state_it : $state_it->getEmptyIterator();
	}

 	function draw( $view = null )
 	{
		$targets = getFactory()->getObject('Transition')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('SourceState', $this->state_it->getId() == '' ? 0 : $this->state_it->getId())
					)
			)->fieldToArray('TargetState');

		$sources = getFactory()->getObject('Transition')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('TargetState', $this->state_it->getId() == '' ? 0 : $this->state_it->getId())
					)
			)->fieldToArray('SourceState');
					
		echo '<span class="input-block-level well well-text">';
			$state_it = $this->state_it->object->getRegistry()->Query(
					array (
							new FilterBaseVpdPredicate()
					)
				);
			echo '<div style="display: table; width: 100%">';
				echo '<div style="display: table-cell; width:13%;">'.text(2014).'</div>';
				echo '<div style="display: table-cell; width:2%;"></div>';
				echo '<div style="display: table-cell; width:35%;">';
					while( !$state_it->end() )
					{
						$checked = $this->state_it->getId() != '' ? in_array($state_it->getId(), $targets) : true;  
						echo '<label class="checkbox">';
			  				echo '<input type="checkbox" class="checkbox" name="ForwardRequired'.$state_it->get('ReferenceName').'" '.($checked ? 'checked' : '').'>';
			  				echo $state_it->getDisplayName();
						echo '</label>';
						$state_it->moveNext();
					}
				echo '</div>';
				$state_it->moveFirst();
				echo '<div style="display: table-cell; width:13%;">'.text(2015).'</div>';
				echo '<div style="display: table-cell; width:2%;"></div>';
				echo '<div style="display: table-cell; width:35%;">';
					while( !$state_it->end() )
					{
						$checked = $this->state_it->getId() != '' ? in_array($state_it->getId(), $sources) : true;  
						echo '<label class="checkbox">';
			  				echo '<input type="checkbox" class="checkbox" name="BackwardRequired'.$state_it->get('ReferenceName').'" '.($checked ? 'checked' : '').'>';
			  				echo $state_it->getDisplayName();
						echo '</label>';
						$state_it->moveNext();
					}
				echo '</div>';
			echo '</div>';
		echo '</span>';
 	}
}