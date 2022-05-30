<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelBuilder.php";
include "persisters/TaskFeaturePersister.php";

class TaskViewModelCommonBuilder extends TaskViewModelBuilder
{
    public function build( Metaobject $object )
    {
   		$object->setAttributeDescription('Fact', 
			str_replace('%1', getFactory()->getObject('Module')->getExact('methodology')->get('Url'),
				str_replace('%2', getFactory()->getObject('PMReport')->getExact('activitiesreport')->get('Url'), 
					text(2009)))
 			);

        if ( $object->getAttributeType('ChangeRequest') != '' ) {
            if ( $object->getAttributeType('Description') == '' ) {
                $request = $object->getAttributeObject('ChangeRequest');
                if ( !getFactory()->getAccessPolicy()->can_read_attribute($request, 'Description') ) {
                    $object->removeAttribute('IssueDescription');
                }
            }
            $object->addAttribute('IssueAttachment', 'REF_pm_AttachmentId', text(2123), false, false, '', 41);
            $object->addPersister( new TaskIssueArtefactsPersister() );

            $object->addAttribute('Feature', 'REF_pm_FunctionId', translate('Функция'), false, false, '', 41);
            $object->addPersister( new TaskFeaturePersister() );

            foreach ( array('IssueAttachment') as $attribute ) {
                $object->addAttributeGroup($attribute, 'source-issue');
            }
        }
    }
}