<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FormAttachmentEmbedded.php';
include_once "FormWikiAttachmentEmbedded.php";

class FormWikiDocumentAttachmentEmbedded extends FormWikiAttachmentEmbedded
{
 	function drawAddButton( $view, $tabindex )
 	{
		if ( $this->getIteratorRef()->count() < 1 ) return;

 		$script = 'javascript: appendEmbeddedItem('.$this->getFormId().');" onkeyup="javascript: if (event.keyCode == 13) { $(this).trigger(\'click\'); }';
 		echo '<div class="embedded-append-link"><a class="dashed embedded-add-button" onclick="'.$script.'">'.text(2081).'</a></div>';
 	}

	function getItemDisplayName( $object_it )
	{
		return '<a href="'.$object_it->getFileUrl().'" name="'.$object_it->getFileName('Content').'">'.
			'<img src="/images/attach.png" > '.$object_it->getFileName('Content').'</a>';
	}

}

 
   