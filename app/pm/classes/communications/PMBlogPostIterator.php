<?php

class PMBlogPostIterator extends BlogPostIterator
{
    function getDisplayName()
    {
        global $project_it;

        $uid = new ObjectUID;
        $codename = $uid->getProject( $this );

        if ( !is_object($project_it) || $project_it->get('CodeName') != $codename )
        {
            $prefix = '{'.$codename.'} ';
        }

        return $prefix.parent::getDisplayName();
    }
}
