<?php

if ( !class_exists('Tag', false) ) include "Tag.php";

include "BlogPostTagIterator.php";
include "persisters/BlogPostTagDetailsPersister.php";

class BlogPostTag extends Tag
{
    function BlogPostTag()
    {
        global $model_factory;
        
        $this->tag = new Tag;
        
        parent::Metaobject('BlogPostTag');
        
        $this->addPersister( new BlogPostTagDetailsPersister() );
        
        if ( !is_object($this->object) )
        {
            $this->object = $model_factory->getObject('BlogPost');
        }
    }

	function createIterator() 
	{
		return new BlogPostTagIterator($this);
	}
	
    function getPageNameObject( $object_id = '' )
    {
        return $this->object->getPage().'&tag='.$object_id;
    }

    function getPages( $tag_id )
    {
        $sql = "SELECT p.BlogPostId " .
                " FROM BlogPostTag wt, Tag t, BlogPost p " .
                "WHERE wt.BlogPost = p.BlogPostId".
                "  AND t.TagId = wt.Tag " .
                "  AND t.TagId = ".$tag_id;

        return $this->createSQLIterator($sql);
    }

    function getTagsByPost( $post_id )
    {
        $tag_it = $this->getByRef('BlogPost', $post_id);
        
        $tag = getFactory()->getObject('Tag');

        if ( $tag_it->count() < 1 ) return $tag->getEmptyIterator(); 
        
        return $tag->getInArray('TagId', $tag_it->fieldToArray('Tag'));
    }

    function getPostsByTag( $tag_id )
    {
        global $model_factory;
        	
        $tag = $model_factory->getObject('Tag');
        
        $tag_it = $tag->getExact($tag_id);
        	
        $tag_it = $this->getByRef('Tag', $tag_it->getId() );
        
        $post = $model_factory->getObject('BlogPost');

        if ( $tag_it->count() < 1 ) return $post->getEmptyIterator();
        
        return $post->getInArray('BlogPostId', $tag_it->fieldToArray('BlogPost'));
    }

    function getAllTags()
    {
        global $project_it;
        	
        $sql = "SELECT t.TagId, t.Caption, COUNT(1) ItemCount " .
                " FROM BlogPostTag wt, Tag t, BlogPost p, pm_Project j " .
                "WHERE wt.BlogPost = p.BlogPostId".
                "  AND t.TagId = wt.Tag " .
                "  AND p.Blog = j.Blog ".
                "  AND j.pm_ProjectId = ".$project_it->getId().
                " GROUP BY t.TagId ORDER BY t.OrderNum ";

        return $this->tag->createSQLIterator($sql);
    }

    function getAllTagsDescOrder()
    {
        global $project_it;
        	
        $sql = "SELECT t.TagId, t.Caption, COUNT(1) ItemCount " .
                " FROM BlogPostTag wt, Tag t, BlogPost p, pm_Project j " .
                "WHERE wt.BlogPost = p.BlogPostId".
                "  AND t.TagId = wt.Tag " .
                "  AND p.Blog = j.Blog ".
                "  AND j.pm_ProjectId = ".$project_it->getId().
                " GROUP BY t.TagId ".
                " ORDER BY t.Caption DESC ";

        return $this->tag->createSQLIterator($sql);
    }

    function getByObject( $object_id )
    {
        return $this->getTagsByPost( $object_id );
    }

    function getByAK( $object_id, $tag_id )
    {
        return $this->getByRefArray(
                array('BlogPost' => $object_id, 'Tag' => $tag_id) );
    }

    function bindToObject( $object_id, $tag_id )
    {
        $this->add_parms(array('BlogPost' => $object_id,
                'Tag' => $tag_id) );
    }
}