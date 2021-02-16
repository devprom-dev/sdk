<?php
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionMaterializeContent extends BusinessActionWorkflow
{
 	function getId() {
 		return '7cf50fcd-ff61-42da-b8de-80e15116f5ff';
 	}
	
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(3014);
 	}

    function apply( $object_it )
    {
        $matches = array();
        if ( !preg_match(REGEX_INCLUDE_PAGE, $object_it->getHtmlDecoded('Description'), $matches) ) return false;

        $parser = WikiEditorBuilder::build()->getHtmlParser();
        $parser->setObjectIt($object_it->copy());
        $description = $parser->parse($object_it->getHtmlDecoded('Description'));

        $object_it->object->setNotificationEnabled(false);
        $object_it->object->getRegistry()->Store($object_it, array(
            'Description' => $description
        ));

        return true;
    }
}