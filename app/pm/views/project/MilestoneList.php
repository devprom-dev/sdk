<?php

class MilestoneList extends PMPageList
{
	function getGroupFields2()
	{
		return array();
	}
	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			case 'Passed':
			case 'Description':
			case 'ReasonToChangeDate':
			case 'CompleteResult':
				return false;

			case 'UID':
				return true;
				
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}
	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}

	function IsNeedToDeleteRow( $object_it )
	{
		return $object_it->get('ObjectClass') == 'pm_Milestone'; 
	}
	
	function getActions( $object_it ) 
	{
	    if ( $object_it->get('ObjectClass') != 'pm_Milestone' ) return array(); 
	        
	    return parent::getActions( $object_it );
	}
	
	function drawCell( $object_it, $attr ) 
	{
		global $model_factory;
		
		switch ( $attr )
		{
			case 'Caption':
				echo $object_it->get('Caption');
				break;
				
			case 'UID':
				if ( $object_it->get('ObjectClass') == 'pm_Milestone' )
				{
					parent::drawCell( $object_it, $attr );
				}
				break;

			case 'MilestoneDate':
				echo $object_it->getDateFormat('MilestoneDate').' '.$this->getMilestoneDate($object_it);
				break;
				
			case 'RecentComment':
				if ( $object_it->get('ObjectClass') == 'pm_Milestone' )
				{
					parent::drawCell( $object_it, $attr );
					
					echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
							'object_it' => $object_it,
							'redirect' => ''
					));
				}
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
				
			default:
				switch ( $attr )
				{
					case 'RecentComment':
						return '5%';
				}
				
				return parent::getColumnWidth( $attr );
		}
	}
}