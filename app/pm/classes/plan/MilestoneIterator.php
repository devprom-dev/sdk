<?php

class MilestoneIterator extends OrderedIterator
{
    function getDisplayName() {
        return $this->get('Caption');
    }

    function getDisplayNameExt( $prefix = '' )
    {
        if ( $this->get('DueWeeks') != '' && $this->get('DueWeeks') < 3 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 1
                            ? 'label-important' : 'label-warning').'">' .
                                $this->getDateFormattedShort('MilestoneDate'). '</span>';
        }
        else {
            $prefix .= $this->getDateFormattedShort('MilestoneDate');
            if ( $this->get('Caption') != '' ) $prefix .= ', ';
        }
        return parent::getDisplayNameExt($prefix);
    }

    function getDisplayNameSearch( $prefix = '' ) {
        return parent::getDisplayNameSearch( $prefix . $this->getDateFormatted('MilestoneDate') );
    }

    function getViewUrl()
    {
 	 	if ( $this->get('ObjectClass')!= '' && $this->get('ObjectClass') != 'pm_Milestone' ) {
 	 		return '';
 	 	}
 	 	else {
 	 		return parent::getViewUrl();
 	 	}
    }
}