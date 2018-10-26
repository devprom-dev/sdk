<?php

class WikiDocumentIterator extends StatableIterator
{
    function getDisplayName()
    {
        $caption = parent::getDisplayName();
        if ( $this->get('DocumentVersion') != '' ) {
            $caption .= ' ['.$this->get('DocumentVersion').']';
        }
        return $caption;
    }
}