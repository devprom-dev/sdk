<?php

include "SCMFileChangeHistoryRegistry.php";
include "predicates/SCMFileChangeHistoryPredicate.php";

class SCMFileChangeHistory extends Metaobject
{
	public function __construct()
	{
		parent::__construct('pm_ScmFileChanges', new SCMFileChangeHistoryRegistry($this));
	}
}