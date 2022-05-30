<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/persisters/IterationArtefactsPersister.php";

class IterationModelArtefactsBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
		if ( !$object instanceof Iteration ) return;
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        if ( getSession()->IsRDD() ) {
            $object->addAttribute('Issues', 'REF_IssueId', translate('Пожелания'), true, false, '', 90);
            $object->addAttribute('Increments', 'REF_IncrementId', text(2032), true, false, '', 95);
            $object->addAttributeGroup('Increments', 'tabincrements');
        }
        else {
            $object->addAttribute('Issues', 'REF_pm_ChangeRequestId', text(808), false, false, '', 90);
        }
		$object->addAttributeGroup('Issues', 'tabissues');

		if ( $methodology_it->HasPlanning() && $methodology_it->HasTasks() ) {
			$object->addAttribute('Tasks', 'REF_pm_TaskId', translate('Задачи'), false, false, '', 100);
			$object->addAttributeGroup('Tasks', 'tabtasks');
		}

		$object->addPersister( new IterationArtefactsPersister() );
    }
}