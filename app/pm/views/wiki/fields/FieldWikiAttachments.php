<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include_once "FormWikiAttachmentEmbedded.php";

class FieldWikiAttachments extends FieldAttachments
{
 	var $form;

    function getFormObject( $object ) {
        return new FormWikiAttachmentEmbedded( $object, 'WikiPage' );
    }

	function getPredicates() {
		if ( $this->getObjectIt()->getId() > 0 ) {
			return array(new FilterAttributePredicate('WikiPage', $this->getObjectIt()->getId()));
		}
		else {
			return array(new FilterAttributePredicate('WikiPage', 0));
		}
	}

	function getForm()
	{
		if ( is_object($this->form) ) return $this->form;

		$files = getFactory()->getObject('WikiPageFile');
		$files->setAttributeType('WikiPage', 'REF_'.get_class($this->getObject()).'Id');

		foreach( $this->getPredicates() as $filter ) {
			$files->addFilter( $filter );
		}

 		$this->form = $this->getFormObject($files);
 		$this->form->setImageClass('modify_image');
 		$this->form->setAnchorIt( $this->getObjectIt() );

 		return $this->form;
	}
}