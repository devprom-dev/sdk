<?php

class BlogPostIterator extends OrderedIterator
{
    function getTags()
    {
        global $model_factory;

        $tag = $model_factory->getObject('BlogPostTag');
        $tag_it = $tag->getTagsByPost( $this->getId() );

        return $tag_it->fieldToArray('Caption');
    }

    function getViewUrl() {
        return getSession()->getApplicationUrl($this).'project/blog?BlogPostId='.$this->getId();
    }
}