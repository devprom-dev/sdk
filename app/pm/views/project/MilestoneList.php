<?php

class MilestoneList extends PMPageList
{
    function getGroupDefault() {
        return 'none';
    }

    function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Caption':
				echo $object_it->get('Caption');
				break;
			case 'MilestoneDate':
				echo $object_it->getDateFormat('MilestoneDate').' '.$this->getMilestoneDate($object_it);
				break;
			default:
				parent::drawCell( $object_it, $attr );	
		}
	}

 	function getMilestoneDate( & $object_it )
 	{
		$language = getLanguage();
		
		if ( $object_it->get('Passed') == 'N' )
		{
			if ( $object_it->get('Overdue') > 0 )
			{
				$date_class = 'label-important';
			}
			else if ( $object_it->get('Overdue') > -4 && $object_it->get('Overdue') <= 0 )
			{
				$date_class = 'label-warning';
			}
			else
			{
				$date_class = 'label-success';
			}
		}

		if ( $object_it->get('Overdue') == 0 )
		{
			$date_caption = translate('сегодня');
		}
		else
		{
			$date_caption = ($object_it->get('Overdue') > 0 ? '-' : '+').
				abs($object_it->get('Overdue')).' '.$language->getDaysWording($object_it->get('Overdue'));
		}
			 
 		return '<span class="label '.$date_class.'">'.$date_caption.'</span>';
 	}

	
	function getColumnWidth( $attr ) 
	{
		switch ( $attr )
		{
			case 'MilestoneDate':
				return '160';
		}
        return parent::getColumnWidth( $attr );
	}
}