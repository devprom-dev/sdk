<?php

class InvitationIterator extends OrderedIterator
{
    function getDisplayName() {
        $title = $this->get('Addressee');
        if ( $this->get('ProjectRole') != '' ) {
            $title .= ' [' . $this->getRef('ProjectRole')->getDisplayName() . ']';
        }
        return $title;
    }
}