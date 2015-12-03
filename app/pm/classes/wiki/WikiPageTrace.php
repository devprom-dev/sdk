<?php

include "WikiPageTraceIterator.php";
include "persisters/WikiPageTraceKeyPersister.php";
include "predicates/WikiTraceSourceReferencePredicate.php";
include "predicates/WikiTraceTargetReferencePredicate.php";
include "predicates/WikiTraceTargetDocumentPredicate.php";
include "predicates/WikiTraceTargetBaselinePredicate.php";
include "predicates/WikiTraceSourceDocumentPredicate.php";
include "WikiPageTraceRegistry.php";
include "WikiPageTraceUnsyncReason.php";

class WikiPageTrace extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('WikiPageTrace', new WikiPageTraceRegistry());

 		$this->addPersister( new WikiPageTraceKeyPersister() );
 		
 		$this->setAttributeType('UnsyncReasonType', 'REF_WikiPageTraceUnsyncReasonId');
 	}
 	
 	function createIterator() 
 	{
 		return new WikiPageTraceIterator( $this );
 	}

	function getBaselineReference()
	{
		return 'Baseline';
	}

	function IsDeletedCascade( $object )
	{
		if ( is_a($object, 'WikiPage') )return true;
		
		return false;
	}
	
	function add_parms( $parms )
	{
		if ( $parms['Type'] == '' ) $parms['Type'] = 'coverage';

		return parent::add_parms( $parms );
	}
	
	public function getTargetReferenceName()
	{
		return '';
	}
}
