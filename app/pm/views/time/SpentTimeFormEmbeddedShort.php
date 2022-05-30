<?php

class SpentTimeFormEmbeddedShort extends SpentTimeFormEmbedded
{
    private $showAutoTimeButtons = true;

	function showAutoTimeButtons( $value = true ) {
	    $this->showAutoTimeButtons = $value;
    }

    function getItemVisibility( $object_it ) {
		return false; 		
	}
	
	function drawAddButton( $view, $tabindex )
	{ 
		$object_it = $this->getIteratorRef();
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
                        'url' => $moduleIt->getUrl('ids='.\TextUtils::buildIds($ids[$key])),
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

		echo '<span>';
            parent::drawAddButton( $view, $tabindex );
        echo '</span>';

        if ( $this->showAutoTimeButtons ) {
            echo '<span class="auto-time-panel embedded-add-button">';
                $this->drawAutoTimes(true);
            echo '</span>';
        }
	}
}
