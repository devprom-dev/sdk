<?php
include_once SERVER_ROOT_PATH.'pm/classes/wiki/PMWikiPage.php';
include "ProjectPageIterator.php";
include "predicates/KnowledgeBaseAccessPredicate.php";
include "sorts/NativeProjectSortClause.php";

class ProjectPage extends PMWikiPage
{
 	function __construct()
 	{
 		parent::__construct();
        foreach ( array('Estimation','Importance') as $attribute ) {
            $this->addAttributeGroup($attribute, 'system');
        }
 	}
 	
 	function getDisplayName() 
 	{
		return translate('База знаний');
	}
	
 	function getAttributes()
	{
		$attrs = parent::getAttributes();
		
		unset( $attrs['PageType'] );
		
		return $attrs;
	}
	
	function getReferenceName() 
	{
		return WikiTypeRegistry::KnowledgeBase;
	}
	
	function createIterator() 
	{
		return new ProjectPageIterator( $this );
	}

	function getRootIt()
	{
		return $this->getRegistry()->Query( array (
				new WikiRootFilter(),
				new FilterVpdPredicate(),
				new NativeProjectSortClause($this->getVpdValue())
		));
	}
	
	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'knowledgebase/tree?';
	}

	function getPageHistory() 
	{
		return getSession()->getApplicationUrl($this).'knowledgebase/tree?view=history&';
	}

	function getSectionName()
    {
        return text(2279);
    }

    function add_parms( $parms )
	{
		if ( $parms['ParentPage'] < 1 )
		{
			$root_it = $this->getRegistry()->Query( array (
				new WikiRootFilter(),
				new FilterVpdPredicate( $this->getVpdValue() )
			));
		
			$parms['ParentPage'] = $root_it->getId();
		}
		
		return parent::add_parms( $parms );
	}
}