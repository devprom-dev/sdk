<?php
include_once SERVER_ROOT_PATH.'pm/classes/wiki/PMWikiPage.php';
include "ProjectPageIterator.php";
include "ProjectPageRegistry.php";
include "sorts/NativeProjectSortClause.php";

class ProjectPage extends PMWikiPage
{
    function __construct($registry = null) {
        parent::__construct(is_object($registry) ? $registry : new ProjectPageRegistry($this));
    }

    function getDisplayName() {
		return translate('База знаний');
	}
	
	function getReferenceName() {
		return WikiTypeRegistry::KnowledgeBase;
	}
	
	function createIterator() {
		return new ProjectPageIterator( $this );
	}

	function getPage() {
		return getSession()->getApplicationUrl($this).'knowledgebase/tree?';
	}

	function getPageHistory() {
		return getSession()->getApplicationUrl($this).'knowledgebase/tree?view=history&';
	}

	function getSectionName() {
        return text(2279);
    }

    function add_parms( $parms )
	{
		if ( $parms['ParentPage'] < 1 ) {
			$root_it = $this->getRegistry()->Query( array (
				new WikiRootFilter(),
				new FilterVpdPredicate( $this->getVpdValue() )
			));
			$parms['ParentPage'] = $root_it->getId();
		}
		return parent::add_parms( $parms );
	}

    protected function reMapState( $vpd, $state ) {
        return '';
    }
}