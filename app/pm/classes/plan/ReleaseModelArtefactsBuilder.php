<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/persisters/ReleaseArtefactsPersister.php";

class ReleaseModelArtefactsBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
		if ( !$object instanceof Release ) return;

		$object->addAttribute('Issues', 'REF_pm_ChangeRequestId', translate('Пожелания'), false, false, '', 90);
		$object->addAttributeGroup('Issues', 'tab-issues');

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasPlanning() && $methodology_it->HasTasks() ) {
			$object->addAttribute('Tasks', 'REF_pm_TaskId', translate('Задачи'), false, false, '', 100);
			$object->addAttributeGroup('Tasks', 'tab-tasks');
		}

		$object->addPersister( new ReleaseArtefactsPersister() );
    }
}