<?php

include_once SERVER_ROOT_PATH."core/classes/resources/ContextResourceBuilder.php";

class PMContextResourceCustomReportsBuilder extends ContextResourceBuilder
{
    function build( ContextResourceRegistry $object )
    {
    	$report_it = getFactory()->getObject('PMCustomReport')->getAll();
    	
    	while( !$report_it->end() )
    	{
    		if ( $report_it->get('Description') != '' )
    		{
    			$object->addText( $report_it->getId(), '<p>'.nl2br($report_it->getHtmlDecoded('Description')).'</p>' );
    		}
    		
    		$report_it->moveNext();
    	}
    }
}