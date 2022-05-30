<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";

class WikiPageDataHashEventHandler extends ObjectFactoryNotificator
{
 	function modify( $prev_object_it, $object_it ) {
		if ( !$object_it->object instanceof WikiPage ) return;
		$this->updateHash($object_it);
	}

	function add( $object_it ) {
        if ( !$object_it->object instanceof WikiPage ) return;
        $this->updateHash($object_it);
	}

 	function delete( $object_it ) {
	}

	protected function updateHash( $object_it )
    {
        $object = getFactory()->getObject(get_class($object_it->object));
        $object->setRegistry(new WikiPageRegistryContent());
        $object_it = $object->getExact($object_it->getId());

 	    $hash = $object_it->buildDataHash();
 	    if ( $hash == $object_it->get('DataHash') ) return;
 	    DAL::Instance()->Query(
 	        "UPDATE WikiPage SET DataHash = '".$hash."' WHERE WikiPageId = " . $object_it->getId());
    }
}
 