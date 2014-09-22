<?php

class BlogPostIterator extends OrderedIterator
{
    function getWithSameTags( $limit = 5 )
    {
        $sql = " SELECT p.* " .
                "   FROM BlogPost p" .
                "  WHERE p.BlogPostId IN " .
                "		(SELECT t1.BlogPost " .
                "		   FROM BlogPostTag t1, " .
                "			    BlogPostTag t2" .
                "          WHERE t1.Tag = t2.Tag" .
                "            AND t2.BlogPost = ".$this->getId().")" .
                "    AND p.BlogPostId <> ".$this->getId().
                "  ORDER BY p.RecordCreated DESC ".
                "  LIMIT ".$limit;

        return $this->object->createSQLIterator($sql);
    }

    function getTags()
    {
        global $model_factory;

        $tag = $model_factory->getObject('BlogPostTag');
        $tag_it = $tag->getTagsByPost( $this->getId() );

        return $tag_it->fieldToArray('Caption');
    }

    function getTagsIt()
    {
        global $model_factory;

        $tag = $model_factory->getObject('BlogPostTag');
        return $tag->getTagsByPost( $this->getId() );
    }
}