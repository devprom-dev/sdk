<?php

class DocumentTemplateRegistry extends ObjectRegistrySQL
{
	function setReferenceName( $referenceName ) {
		$this->referenceName = $referenceName;
	}

	function getFilters() {
		if ( $this->referenceName == '' ) return parent::getFilters();
		return array_merge(
			parent::getFilters(),
			array (
				new FilterAttributePredicate('ReferenceName', $this->referenceName)
			)
		);
	}

	private $referenceName = '';
}