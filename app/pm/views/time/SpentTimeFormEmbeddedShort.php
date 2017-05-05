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
		$moduleIt = getFactory()->getObject('Module')->getExact('worklog');
		
		$items = array();
		$ids = array();

		$object_it->moveFirst();
		while( !$object_it->end() )
		{
		    $key = $object_it->getRef('Participant')->getDisplayName();
			$items[$key] += $object_it->get('Capacity');
            $ids[$key][] = $object_it->getId();
			$object_it->moveNext();
		}
		
		$lines = array();
		foreach( $items as $key => $item ) {
            $lines[] = $view->render('core/EmbeddedRowTitleMenu.php', array (
                'title' => $key.'&nbsp;('.getSession()->getLanguage()->getHoursWording($item).')',
                'items' => array(
                    array(
                        'uid' => 'activity-edit',
                        'url' => $moduleIt->getUrl('activitytask='.join('-',$ids[$key])),
                        'name' => translate('Редактировать')
                    )
                )
            ));
		}
		
		if ( count($lines) > 0 )
		{
			echo '<div class="btn-group" style="vertical-align:top;" title="'.text(1874).'">';
    			echo join('<br/>', $lines);
			echo '</div><br/>';
		}		
	
		parent::drawAddButton( $view, $tabindex );

        if ( count($lines) > 0 ) {
            $url = $moduleIt->getUrl('activitytask='.join('-',$object_it->idsToArray()));
            echo '<a class="dashed embedded-add-button" style="margin-left:20px;" target="_blank" href="'.$url.'" tabindex="-1">';
                echo translate('подробнее');
            echo '</a>';
        }
	}
}
