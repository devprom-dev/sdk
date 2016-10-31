<?php

include_once "WebMethod.php";

class ExportWebMethod extends WebMethod
{
	function getRedirectUrl()
	{
		global $_SERVER;

		$parts = preg_split('/\&/', $_SERVER['QUERY_STRING']);
		
		foreach ( array_keys($parts) as $key )
		{ 
			if ( strpos($parts[$key], 'project=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'offset') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'namespace=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'module=') !== false )
			{
				unset($parts[$key]);
			}
		}
		
		return '?'.join($parts, '&');
	}
	
 	function execute_request()
 	{
        $title = '';
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