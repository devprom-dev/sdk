<?php

class WorkItemIterator extends StatableIterator
{
    function getDisplayNameExt( $prefix = '' )
    {
        if ( $this->get('DueDate') != '' && $this->get('DueWeeks') < 4 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'">';
            $prefix .= $this->getDateFormatShort('DueDate');
            $prefix .= '</span> ';
        }

        $title = $prefix . parent::getWordsOnly('Caption', 8);

        $priorityColor = parent::get('PriorityColor');
        if ( $priorityColor == '' ) $priorityColor = 'white';
        $title = '<span class="pri-cir" style="color:'.$priorityColor.'">&#x26AB;</span>' . $title;

        if ( $this->get('TagNames') != '' ) {
            $tags = array_map(function($value) {
                return ' <span class="label label-info label-tag">'.$value.'</span> ';
            }, preg_split('/,/', $this->get('TagNames')));
            $title = join('',$tags) . $title;
        }

        return $title;
    }
}