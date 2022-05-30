<?php
include "WikiFileIterator.php";
include "predicates/WikiFileReferenceFilter.php";

class WikiPageFile extends Metaobject
{
 	function __construct()
 	{
		parent::__construct('WikiPageFile');
        $this->addAttribute('ContentExt', 'VARCHAR', '', false, true);
        $this->addAttribute('ContentPath', 'VARCHAR', '', false, true);
        $this->addAttribute('ContentMime', 'VARCHAR', '', false, true);

        $this->addAttributeGroup('WikiPage', 'alternative-key');
        $this->addAttributeGroup('ContentExt', 'alternative-key');
	}

 	function createIterator() {
 		return new WikiFileIterator( $this );
 	}

	function modify_parms( $object_id, $parms )
	{
		$result = parent::modify_parms( $object_id, $parms );
		
		$file_it = $this->getExact($object_id);
		$page_it = $file_it->getRef('WikiPage');
		
		$page_it->onFileChanged( $file_it );
		
		return $result;
	}
}