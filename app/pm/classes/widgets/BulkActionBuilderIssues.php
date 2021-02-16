<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";
include_once SERVER_ROOT_PATH."pm/methods/CreateIssueBasedOnWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/BindIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/MergeIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";

class BulkActionBuilderIssues extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();

 		if ( $object instanceof Request || $object instanceof Issue ) {
            $method = new CreateIssueBasedOnWebMethod($object);
            if ( $method->hasAccess() ) {
                $registry->addActionUrl($method->getCaption(), $method->url(array('getCheckedRows')));
            }
            $method = new MergeIssuesWebMethod();
            if ($method->hasAccess()) {
                $registry->addCustomAction($method->getCaption(), $method->getMethodName());
            }
        }

        if ( $object instanceof Request || $object instanceof Increment ) {
            $method = new BindIssuesWebMethod();
            if ($method->hasAccess()) {
                $registry->addCustomAction($method->getCaption(), $method->getMethodName());
            }
        }

 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
 		
 		$registry->addCustomAction(text(861), 'Method:SetTagsRequestWebMethod:Tag');
 		$registry->addCustomAction(text(862), 'Method:SetTagsRequestWebMethod:RemoveTag');

        $registry->addCustomAction(text(2632), 'Method:SetWatchersWebMethod:Watchers');
        $registry->addCustomAction(text(2633), 'Method:SetWatchersWebMethod:RemoveWatchers');

		$method = new DuplicateIssuesWebMethod();
		if ( $method->hasAccess() ) $registry->addCustomAction($method->getCaption(), $method->getMethodName());

		$reportIt = getFactory()->getObject('PMReport')->getExact('workflowanalysis');
        $registry->addActionUrl($reportIt->getDisplayName(),
            "javascript: openURLItems('".$reportIt->getUrl('request=%ids%')."');"
        );
 	}
}