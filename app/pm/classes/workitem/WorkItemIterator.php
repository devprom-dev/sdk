<?php

class WorkItemIterator extends StatableIterator
{
    private $objects = array();

    function getDisplayNameExt( $prefix = '' )
    {
        if ( $this->get('DueDate') != '' && $this->get('DueWeeks') > -3 && $this->get('DueWeeks') < 4 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'">';
            $prefix .= $this->getDateFormattedShort('DueDate');
            $prefix .= '</span> ';
        }

        $title = $prefix . parent::getWordsOnly('Caption', 8);

        $priorityColor = parent::get('PriorityColor');
        if ( $priorityColor == '' ) $priorityColor = 'white';
        $title = '<span class="pri-cir" style="color:'.$priorityColor.'">&#x25cf;</span>' . $title;

        if ( $this->get('TagNames') != '' ) {
            $tags = array_map(function($value) {
                return ' <span class="label label-info label-tag">'.$value.'</span> ';
            }, preg_split('/,/', $this->get('TagNames')));
            $title = join('',$tags) . $title;
        }

        return $title;
    }

    function getObjectIt()
    {
        $data = $this->getData();
        if ( !is_object($this->objects[$this->get('ObjectClass')]) ) {
            $this->objects[$this->get('ObjectClass')] = getFactory()->getObject($this->get('ObjectClass'));
        }
        $object = $this->objects[$this->get('ObjectClass')];
        $data[$object->getIdAttribute()] = $this->getId();
        return $object->createCachedIterator(array($data));
    }
}