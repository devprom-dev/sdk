<?php
include "DocumentTemplateIterator.php";
include "DocumentTemplateRegistry.php";

class DocumentTemplate extends Metaobject
{
 	function __construct( $object = null ) {
		$registry = new DocumentTemplateRegistry($this);
		if ( is_object($object) ) {
			$this->object = $object;
			$registry->setReferenceName($object->getReferenceName());
		}
		parent::__construct('pm_DocumentTemplate', $registry);
	}

 	function createIterator() {
 		return new DocumentTemplateIterator($this);
 	}

	function add_parms( $parms ) {
		if ( $parms['ReferenceName'] == '' ) {
			$parms['ReferenceName'] = $this->object->getReferenceName();
		}
		return parent::add_parms( $parms );
	}

	private $object = null;
}