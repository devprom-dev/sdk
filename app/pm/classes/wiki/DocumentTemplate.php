<?php
include "DocumentTemplateIterator.php";
include "DocumentTemplateRegistry.php";

class DocumentTemplate extends Metaobject
{
 	function __construct( $reference = null )
    {
		$registry = new DocumentTemplateRegistry($this);
		if ( is_object($reference) ) {
			$this->reference = $reference;
			$registry->setReferenceName($reference->getReferenceName());
		}
		parent::__construct('pm_DocumentTemplate', $registry);
        $this->addAttributeGroup('Content', 'skip-mapper');
		$this->addAttributeGroup('ReferenceName', 'system');
	}

 	function createIterator() {
 		return new DocumentTemplateIterator($this);
 	}

	function add_parms( $parms ) {
		if ( $parms['ReferenceName'] == '' ) {
			$parms['ReferenceName'] = $this->reference->getReferenceName();
		}
		return parent::add_parms( $parms );
	}

	private $reference = null;
}