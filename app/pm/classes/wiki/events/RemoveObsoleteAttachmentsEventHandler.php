<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";

class RemoveObsoleteAttachmentsEventHandler extends ObjectFactoryNotificator
{
 	function modify( $prev_object_it, $object_it )
	{
		if ( !$object_it->object instanceof WikiPage ) return;

		$remove_ids = array_diff($this->getAttachments($prev_object_it), $this->getAttachments($object_it));
		if ( count($remove_ids) < 1 ) return;

		$file_it = getFactory()->getObject('WikiPageFile')->getRegistry()->Query(
				array (
					new FilterAttributePredicate('WikiPage', $object_it->getId()),
					new FilterInPredicate($remove_ids)
				)
		);
		while( !$file_it->end() ) {
			$file_it->object->delete($file_it->getId());
			$file_it->moveNext();
		}
	}

	protected function getAttachments( $object_it ) {
		$matches = array();
		$files = array();
		if ( preg_match_all('/file\/WikiPageFile\/(\d+)/i', $object_it->getHtmlDecoded('Content'), $matches) ) {
			$files = array_merge($files, $matches[1]);
		}
		if ( preg_match_all('/file\/WikiPageFile\/[^\/]+\/(\d+)/i', $object_it->getHtmlDecoded('Content'), $matches) ) {
			$files = array_merge($files, $matches[1]);
		}
		return $files;
	}

	function add( $object_it ) {
	}

 	function delete( $object_it ) {
	}
}
 