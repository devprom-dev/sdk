<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include_once "FieldWikiAttachments.php";
include "FormWikiDocumentAttachmentEmbedded.php";

class FieldWikiDocumentAttachment extends FieldWikiAttachments
{
	private $baseline_it = null;

	function setBaseline( $baseline_it ) {
		$this->baseline_it = $baseline_it;
	}

	function getFormObject( $object ) {
 		return new FormWikiDocumentAttachmentEmbedded( $object, 'WikiPage' );
	}

	function getPredicates() {
		if ( !is_object($this->baseline_it) ) return parent::getPredicates();
		return array_merge(
			parent::getPredicates(),
			array (
				new FilterSubmittedBeforePredicate($this->baseline_it->get('RecordCreated'))
			)
		);
	}
}