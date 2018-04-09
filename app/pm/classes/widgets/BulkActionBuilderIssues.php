<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";
include_once SERVER_ROOT_PATH."pm/methods/CreateIssueBasedOnWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/BindIssuesWebMethod.php";

class BulkActionBuilderIssues extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();

        $method = new CreateIssueBasedOnWebMethod($object);
        if ( $method->hasAccess() ) {
            $registry->addActionUrl($method->getCaption(), $method->url(array('getCheckedRows')));
        }
        $method = new BindIssuesWebMethod();
        if ( $method->hasAccess() ) {
            $registry->addCustomAction($method->getCaption(), $method->getMethodName());
        }

 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
 		
 		$registry->addCustomAction(text(861), 'Method:SetTagsRequestWebMethod:Tag');
 		$registry->addCustomAction(text(862), 'Method:SetTagsRequestWebMethod:RemoveTag');
 		
		$method = new DuplicateIssuesWebMethod();
		if ( $method->hasAccess() ) $registry->addCustomAction(text(867), $method->getMethodName());

		$reportIt = getFactory()->getObject('PMReport')->getExact('workflowanalysis');
        $registry->addActionUrl($reportIt->getDisplayName(),
            "javascript: openURLItems('".$reportIt->getUrl('request=%ids%')."');"
        );
 	}
}