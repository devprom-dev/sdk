<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class StageModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Stage ) return;
    	
    	$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$object->addAttribute('Stage', 'TEXT', translate('Стадия проекта'), true, true, '', 10);
		$object->addAttribute('Deadlines', 'TEXT', translate('Сроки'), true, false, '', 20);
		$object->addAttribute('EstimatedStartDate', 'DATE', translate('Оценка начала'), false, false, '', 30);
		$object->addAttribute('EstimatedFinishDate', 'DATE', translate('Оценка окончания'), false, false, '', 40);
		$object->addAttribute('ActualStartDate', 'DATE', translate('Начало по плану'), false, true, '', 50);
		$object->addAttribute('ActualFinishDate', 'DATE', translate('Окончание по плану'), false, true, '', 60);

        if ( $methodology_it->get('IsRequirements') == ReqManagementModeRegistry::RDD ) {
            $object->addAttribute('Increments', 'REF_pm_ChangeRequestId', translate('Реализация'), true, false, '', 95);
        }
        else {
            $object->addAttribute('Issues', 'REF_pm_ChangeRequestId', translate('Пожелания'), true, false, '', 90);
        }

		if ( $methodology_it->HasPlanning() && $methodology_it->HasTasks() ) {
			$object->addAttribute('Tasks', 'REF_pm_TaskId', translate('Задачи'), true, false, '', 100);
		}

		$hidden = array ('StartDate', 'FinishDate', 'InitialVelocity', 'Caption', 'Description');
		foreach( $hidden as $attribute ) {
		    $object->setAttributeVisible($attribute, false);
		}

        $trace = array ('Increments', 'Issues', 'Tasks', 'Artefacts');
        foreach( $trace as $attribute ) {
            $object->addAttributeGroup($attribute, 'trace');
        }

        $object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), true);
    }
}