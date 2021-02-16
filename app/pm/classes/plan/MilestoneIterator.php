<?php

class MilestoneIterator extends OrderedIterator
{
 	 function getDisplayName()
 	 {
		$title = $this->getDateFormattedShort('MilestoneDate');

         if ( $this->get('DueWeeks') != '' && $this->get('DueWeeks') < 3 ) {
             $title = '<span class="label '.($this->get('DueWeeks') < 1 ? 'label-important' : 'label-warning').'">' . $title . '</span>';
         }
         else {
             $title = '['.$title.']';
         }

		$caption = $this->get('Caption');
		if ( $caption != '' ) $title .= ' '.$caption;
		
		return $title;
 	 }

 	 function getViewUrl()
 	 {
 	 	if ( $this->get('ObjectClass')!= '' && $this->get('ObjectClass') != 'pm_Milestone' )
 	 	{
 	 		return '';
 	 	}
 	 	else
 	 	{
 	 		return parent::getViewUrl();
 	 	}
 	 }
}