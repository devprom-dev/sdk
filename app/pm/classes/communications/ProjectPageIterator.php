<?php

class ProjectPageIterator extends PMWikiPageIterator
{
    function getDisplayNameExt()
    {
        $title = parent::getDisplayNameExt();
        if ( $this->get('ParentPage') == '' && $this->get('VPD') != getSession()->getProjectIt()->get('VPD') ) {
            $projectIt = $this->getRef('Project');
            $title = $projectIt->getDisplayName() . ': ' . $title;
        }
        return $title;
    }
}
 