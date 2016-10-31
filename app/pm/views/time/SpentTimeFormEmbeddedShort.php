<?php

class SpentTimeFormEmbeddedShort extends SpentTimeFormEmbedded
{
	function getItemVisibility( $object_it )
	{
		return false; 		
	}
	
	function drawAddButton( $view, $tabindex )
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
			$lines[] = $key.'&nbsp;('.getSession()->getLanguage()->getHoursWording($item).')';
		}
		
		if ( count($lines) > 0 )
		{
			echo '<div class="btn-group" style="vertical-align:top;" title="'.text(1874).'">';
				echo '<div class="btn dropdown-toggle transparent-btn spent-short" data-toggle="dropdown" href="#" style="display:table;width:auto;" onclick="uiShowSpentTime();">';
					echo '<span class="title" style="display:table-cell;">'.join('<br/>', $lines).'</span>';
				echo '</div>';
			echo '</div><br/>';
		}		
	
		parent::drawAddButton( $view, $tabindex );

        if ( count($lines) > 0 && $_REQUEST['formonly'] == '' ) {
            $target = defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? '' : '_blank';
            echo '<a class="dashed embedded-add-button" style="margin-left:20px;" target="'.$target.'" href="javascript:uiShowSpentTime();" tabindex="-1">';
                echo translate('подробнее');
            echo '</a>';
        }
	}
}
