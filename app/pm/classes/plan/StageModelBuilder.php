<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class StageModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Stage ) return;
    	
    	$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$object->addAttribute('Stage', '', translate('Стадия проекта'), true, true, '', 10);
		$object->addAttribute('Deadlines', '', translate('Сроки'), true, false, '', 20);
		$object->addAttribute('EstimatedStartDate', 'DATETIME', translate('Оценка начала'), false, false, '', 30);
		$object->addAttribute('EstimatedFinishDate', 'DATETIME', translate('Оценка окончания'), false, false, '', 40);
		$object->addAttribute('ActualStartDate', 'DATE', translate('Начало по плану'), false, true, '', 50);
		$object->addAttribute('ActualFinishDate', 'DATE', translate('Окончание по плану'), false, true, '', 60);
		$object->addAttribute('Burndown', '', translate('Burndown'), true, false, '', 70);
		
		$object->addAttribute('Issues', 'REF_pm_ChangeRequestId', translate('Пожелания'), true, false, '', 90);
		if ( $methodology_it->HasPlanning() && $methodology_it->HasTasks() ) {
			$object->addAttribute('Tasks', 'REF_pm_TaskId', translate('Задачи'), true, false, '', 100);
		}

		$object->addAttribute('Indexes', '', translate('Показатели'), true, false, '', 900);
		
		$hidden = array ('StartDate', 'FinishDate', 'InitialVelocity', 'Caption', 'Description');
		foreach( $hidden as $attribute ) {
		    $object->setAttributeVisible($attribute, false);
		}
    }
}