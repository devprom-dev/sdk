<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/StatePlanFactPersister.php";

class StageModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Stage ) return;

    	$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
        $object->setAttributeCaption('Caption', translate('Стадия'));

		$release = getFactory()->getObject('Release');
        $iteration = getFactory()->getObject('Iteration');
        $hasDeadlines =
            getFactory()->getAccessPolicy()->can_read_attribute($release, 'StartDate')
            && getFactory()->getAccessPolicy()->can_read_attribute($release, 'FinishDate')
            && getFactory()->getAccessPolicy()->can_read_attribute($iteration, 'StartDate')
            && getFactory()->getAccessPolicy()->can_read_attribute($iteration, 'FinishDate');

        if ( $hasDeadlines ) {
            $object->addAttribute('Deadlines', 'DATE', translate('Сроки'), true, false, '', 20);
            $object->addAttribute('EstimatedStartDate', 'DATE', translate('Оценка начала'), false, false, '', 30);
            $object->addAttribute('EstimatedFinishDate', 'DATE', translate('Оценка окончания'), false, false, '', 40);
            $object->addAttribute('ActualStartDate', 'DATE', translate('Начало по плану'), false, true, '', 50);
            $object->addAttribute('ActualFinishDate', 'DATE', translate('Окончание по плану'), false, true, '', 60);
        }

        if ( getSession()->IsRDD() ) {
            $object->addAttribute('Issues', 'REF_IssueId', translate('Пожелания'), true, false, '', 90);
            $object->addAttribute('Increments', 'REF_IncrementId', text(1805), true, false, '', 95);
        }
        else {
            $object->addAttribute('Issues', 'REF_pm_ChangeRequestId', text(808), true, false, '', 90);
        }

        if ( $methodology_it->HasPlanning() ) {
            $object->addAttribute('Tasks', 'REF_pm_TaskId', translate('Задачи'), true, false, '', 100);
        }

		$hidden = array ('StartDate', 'FinishDate', 'InitialVelocity', 'Description');
		foreach( $hidden as $attribute ) {
		    $object->setAttributeVisible($attribute, false);
		}

        $trace = array ('Increments', 'Issues', 'Tasks', 'Artefacts');
        foreach( $trace as $attribute ) {
            $object->addAttributeGroup($attribute, 'trace');
        }

        $object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), true);
        $object->removeAttribute('InitialVelocity');

        $object->addAttribute('ParentStage', 'INTEGER', '', false);
        $object->addAttributeGroup('ParentStage', 'system');
        $object->addAttribute('ParentStageClass', 'VARCHAR', '', false);
        $object->addAttributeGroup('ParentStageClass', 'system');

        if ( $methodology_it->IsReportsRequiredOnActivities() ) {
            $object->addAttribute('Planned', 'FLOAT', translate('Запланировано'), true, false, '', 600);
            $object->addAttribute('Fact', 'FLOAT', translate('Затрачено'), true, false, '', 610);
            foreach ( array('Planned','Fact') as $attribute ) {
                $object->addAttributeGroup($attribute, 'workload');
                $object->addAttributeGroup($attribute, 'hours');
            }
            $object->addPersister(new StatePlanFactPersister());
        }

    }
}