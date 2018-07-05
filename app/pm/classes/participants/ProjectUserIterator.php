<?php

class ProjectUserIterator extends UserIterator
{
    function getDisplayNameExt( $prefix = '' )
    {
        if ( defined('SKIP_WELCOME_PAGE') && SKIP_WELCOME_PAGE ) return parent::getDisplayNameExt();

        $title = parent::getDisplayNameExt();
        if ( $this->get('LeftWork') > 0 ) {
            if ( $prefix != '' ) {
                $iterationIt = getFactory()->getObject('Iteration')->getExact($prefix);
                $leftWork = $iterationIt->getLeftWorkParticipant( $this->getId() );
                $capacity = $iterationIt->getLeftDuration() * $this->get('Capacity');
                if ( $capacity > $leftWork ) {
                    return $title . sprintf(text(2495), round($capacity - $leftWork, 0));
                }
                else {
                    return $title . sprintf(text(2496), round($leftWork - $capacity, 0));
                }
            }
            return $title . sprintf(text(2494), $this->get('LeftWork'));
        }
        else {
            return $title;
        }
    }
}