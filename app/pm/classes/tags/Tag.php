<?php

include "TagIterator.php";
include "persisters/TagKnowledgeBasePersister.php";
include "persisters/TagBlogPostPersister.php";
include "persisters/TagFeaturePersister.php";
include "persisters/TagQuestionPersister.php";
include "persisters/TagRequestPersister.php";

class Tag extends Metaobject
{
 	function Tag() 
 	{
 		parent::Metaobject('Tag');
 		
 		$this->defaultsort = 'Caption ASC';
 	}
 	
 	function extendMetadata()
 	{
 	    global $model_factory;
 	    
 	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 	    
 		$this->addAttribute('Issues', 'REF_pm_ChangeRequestId', translate('Пожелания'), true);
		$this->addPersister( new TagRequestPersister() );

		$feature = $model_factory->getObject('Feature');
		
		if ( getFactory()->getAccessPolicy()->can_read($feature) && $methodology_it->HasFeatures() )
		{
			$this->addAttribute('Features', 'REF_pm_FunctionId', translate('Функции'), true);
			$this->addPersister( new TagFeaturePersister() );
		}

		$this->addAttribute('Questions', 'REF_pm_QuestionId', translate('Вопросы'), true);
		$this->addPersister( new TagQuestionPersister() );
		
		$knowledge = $model_factory->getObject('ProjectPage');
		
		if ( getFactory()->getAccessPolicy()->can_read($knowledge) )
		{
			$this->addAttribute('KnowledgeBase', 'REF_ProjectPageId', translate('База знаний'), true);
			$this->addPersister( new TagKnowledgeBasePersister() );
		}
		
		$blog = $model_factory->getObject('Blog');
		
		if ( getFactory()->getAccessPolicy()->can_read($blog) )
		{
			$this->addAttribute('BlogPosts', 'REF_BlogPostId', translate('Сообщения блога'), true);
			$this->addPersister( new TagBlogPostPersister() );
		}
 	}
 	
 	function createIterator()
 	{
 		return new TagIterator($this);
 	}
 	
 	function getGroupKey()
 	{
 	}
 	
 	function getByObject( $object_id )
 	{
 	}
 	
 	function getByAK( $object_id, $tag_id )
 	{
 	}
 	
 	function getAllTags()
 	{
 		global $model_factory;
 		
		$sql = "SELECT t.Caption, t.Owner " .
				" FROM Tag t " .
				"WHERE t.vpd IN ('".join("','",$this->getVpds())."') ".
				" ORDER BY t.Caption " ;

		return $this->createSQLIterator($sql);
 	}

	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'project/tags?';
	}
}