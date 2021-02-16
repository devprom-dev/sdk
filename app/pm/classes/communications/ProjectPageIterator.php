<?php

class ProjectPageIterator extends PMWikiPageIterator
{
    function getDisplayNameExt($prefix = '', $baselineId = 0)
    {
        $title = parent::getDisplayNameExt($prefix, $baselineId);
        if ( $this->get('ParentPage') == '' && $this->get('VPD') != getSession()->getProjectIt()->get('VPD') ) {
            $projectIt = $this->getRef('Project');
            $title = $projectIt->getDisplayName() . ': ' . $title;
        }
        return $title;
    }
}
 