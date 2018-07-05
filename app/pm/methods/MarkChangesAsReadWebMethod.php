<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class MarkChangesAsReadWebMethod extends WebMethod
{
 	function execute_request()
 	{
 	    if ( $_REQUEST['objects'] == '' ) return;

 	    $ids = TextUtils::parseIds($_REQUEST['objects']);
 	    if ( count($ids) < 1 ) return;

 	    $logIt = getFactory()->getObject('ObjectChangeLog')->getRegistry()->Query(
 	        array(
 	            new FilterInPredicate($ids)
            )
        );
 	    while( !$logIt->end() ) {
 	        if ( $logIt->get('ObjectId') != "" && $logIt->get('ClassName') != "" ) {
                DAL::Instance()->Query(
                    " DELETE FROM ObjectChangeNotification 
                   WHERE ObjectId = ".$logIt->get('ObjectId')." 
                     AND LCASE(ObjectClass) = '".$logIt->get('ClassName')."' 
                     AND SystemUser = ".getSession()->getUserIt()->getId()
                );
            }
            $logIt->moveNext();
        }
	}
}