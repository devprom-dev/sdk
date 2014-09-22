<?php

class BlogIterator extends OrderedIterator
{
    function getPublishedPosts( $month = '', $year = '' )
    {
        $post = getFactory()->getObject('BlogPost');
        
        $post->defaultsort = 'RecordCreated DESC';

        if($month == '' || $year == '') {
            return $post->getByRefArray(
                    array('Blog' => $this->getId(),
                            'IsPublished' => "Y" )
            );
        } else {
            return $post->getByRefArray(
                    array('Blog' => $this->getId(),
                            'IsPublished' => "Y",
                            'EXTRACT(YEAR FROM RecordCreated)' => $year,
                            'EXTRACT(MONTH FROM RecordCreated)' => $month)
            );
        }
    }
}