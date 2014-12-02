<?php

include_once SERVER_ROOT_PATH."core/classes/resources/ContextResourceBuilder.php";

class PMContextResourceBuilder extends ContextResourceBuilder
{
    function build( ContextResourceRegistry $object )
    {
    	if ( getFactory()->getObject('Module')->getExact('ee/msproject')->getId() != '' )
 		{
 			$object->addText( 
 					'project-plan-hierarchy', 
 					text('ee221').'<br/><br/><img src="/plugins/ee/resources/msproject.png"><br/><br/>'.text('ee222')
 			);
 		}
 		
 		$module_it = getFactory()->getObject('Module')->getExact('workflow-issuestate');
 		$object->addText( 'issues-board', 
 					preg_replace('/%1/', 
 							'<a href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>', text(1836))
 			);

 		$module_it = getFactory()->getObject('Module')->getExact('workflow-taskstate');
 		$object->addText( 'tasks-board', 
 					preg_replace('/%1/', 
 							'<a href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>', text(1836))
 			);
 		
 		if ( getFactory()->getAccessPolicy()->can_modify(getFactory()->getObject('pm_Methodology')) )
 		{
	 		$module_it = getFactory()->getObject('Module')->getExact('methodology');
	 		$object->addText( 'requestform-new',
	 					preg_replace('/%2/', "javascript:setupFormFieldsTour.init().restart();setupFormFieldsTour.init().start(true);",  
	 							preg_replace('/%1/', $module_it->get('Url'), text(1875)))
	 			);
 		}
    }
}