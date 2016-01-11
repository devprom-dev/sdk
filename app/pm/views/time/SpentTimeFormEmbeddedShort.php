<?php

class SpentTimeFormEmbeddedShort extends SpentTimeFormEmbedded
{
	function getItemVisibility( $object_it )
	{
		return false; 		
	}
	
	function drawAddButton( $tabindex )
	{ 
		$object_it =& $this->getIteratorRef();
		
		$items = array();
		$object_it->moveFirst();
		while( !$object_it->end() )
		{
			$items[$object_it->getRef('Participant')->getDisplayName()] += $object_it->get('Capacity');
			$object_it->moveNext();
		}
		
		$lines = array();
		foreach( $items as $key => $item )
		{
			$lines[] = $key.'&nbsp;('.getSession()->getLanguage()->getDurationWording($item, 8).')';
		}
		
		if ( count($lines) > 0 )
		{
			echo '<div class="btn-group" style="vertical-align:top;" title="'.text(1874).'">';
				echo '<div class="btn dropdown-toggle transparent-btn spent-short" data-toggle="dropdown" href="#" style="display:table;width:auto;" onclick="javascript:uiShowSpentTime();">';
					echo '<span class="title" style="display:table-cell;">'.join('<br/>', $lines).'</span>';
				echo '</div>';
			echo '</div><br/>';
		}		
	
		parent::drawAddButton( $tabindex );
	}
}
