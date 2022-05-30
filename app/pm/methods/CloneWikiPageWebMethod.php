<?php
include_once "DuplicateWebMethod.php";

class CloneWikiPageWebMethod extends DuplicateWebMethod
{
	function getMethodName()
	{
		return 'Method:'.get_class($this).':Project:class='.get_class($this->getObjectIt()->object);
	}

	function getCaption() {
        return text(2447);
    }

    function hasAccess() {
		return getFactory()->getAccessPolicy()->can_create($this->getObject());
	}

    protected function buildContext()
    {
        $context = parent::buildContext();
        $context->setResetUids(true);
        $context->setResetBaseline(true);
        return $context;
    }

    protected function getSourceIt()
    {
        $object = $this->getObject();

        if ( $_REQUEST['Snapshot'] != '' )
        {
            $version_it = getFactory()->getObject('Snapshot')->getExact($_REQUEST['Snapshot']);
            $registry = new WikiPageRegistryVersion();
            $registry->setSnapshotIt($version_it);
            $registry->setDocumentIt($this->getObjectIt());
            $object->setRegistry($registry);
        }

        $object->addFilter( new ParentTransitiveFilter($this->getObjectIt()->idsToArray()) );
        $object->addSort( new SortDocumentClause() );

        return $object->getAll();
    }

    function getReferences()
    {
        $references = array();
        $references[] = getFactory()->getObject('WikiTypeBase');

        $object_it = $this->getSourceIt();
        $object_it->object->setRegistry(new WikiPageRegistryContent());
        $ids = $object_it->idsToArray();
        $references[] = $object_it->object;

        $attachment = getFactory()->getObject('WikiPageFile');
        $attachment->addFilter( new FilterAttributePredicate('WikiPage', $ids) );
        $references[] = $attachment;

        $tag = getFactory()->getObject('WikiTag');
        $tag->addFilter( new FilterAttributePredicate('Wiki', $ids) );
        $references[] = $tag;

        return $references;
    }
}
