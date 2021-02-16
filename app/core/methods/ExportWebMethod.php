<?php
include_once "WebMethod.php";

class ExportWebMethod extends WebMethod
{
	function getRedirectUrl() {
		return \SanitizeUrl::getSelfUrl();
	}
	
 	function execute_request()
 	{
        $title = '';
        if ( $_REQUEST['objects'] == '' ) {
            $_REQUEST['objects'] = $_REQUEST['object'];
            if ( $_REQUEST['objects'] == '' ) {
                $_REQUEST['objects'] = $_REQUEST['id'];
            }
        }

        $className = getFactory()->getClass($_REQUEST['entity']);
        if ( class_exists($className) ) {
            $object_it = getFactory()->getObject($className)->getExact($_REQUEST['objects']);
            if ( $object_it->count() == 1 ) {
                $title = '&caption='.urlencode($object_it->getHtmlDecoded('Caption'));
            }
        }

        echo '&export=html&class='.SanitizeUrl::parseUrl($_REQUEST['class']).
 			 '&objects='.SanitizeUrl::parseUrl($_REQUEST['objects']).
 			 '&entity='.SanitizeUrl::parseUrl($_REQUEST['entity']).
             $title.
 			 '&redirect='.urlencode(urlencode($_REQUEST['redirect']));
 	}
}