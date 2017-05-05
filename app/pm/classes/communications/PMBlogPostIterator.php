<?php

class PMBlogPostIterator extends BlogPostIterator
{
    function getDisplayName()
    {
        $codename = $this->get('ProjectCodeName');
        $project_it = getSession()->getProjectIt();

        if ( !is_object($project_it) || $project_it->get('CodeName') != $codename ) {
            $prefix = '{'.$codename.'} ';
        }
        return $prefix.parent::getDisplayName();
    }
}
