<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class StageModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Stage ) return;
    	
    	$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( $methodology_it->HasVersions() )
		{
			$object->addAttribute('VersionNumber', 'TEXT', translate('Версия'), true, true, '', 10);
			
			$object->addAttribute('Stage', '', translate('Стадия проекта'), true, true, '', 15);
		}
		else
		{
			$object->addAttribute('Stage', '', translate('Стадия проекта'), true, true, '', 10);
		}

		$object->addAttribute('Deadlines', '', translate('Сроки'), true);
		
		$object->addAttribute('EstimatedStartDate', 'DATETIME', translate('Оценка начала'), false, false);
		
		$object->addAttribute('EstimatedFinishDate', 'DATETIME', translate('Оценка окончания'), false, false);
		
		$object->addAttribute('ActualStartDate', 'DATE', translate('Начало по плану'), false, true);
		
		$object->addAttribute('ActualFinishDate', 'DATE', translate('Окончание по плану'), false, true);
		
		$object->addAttribute('Burndown', '', translate('Burndown'), true);
		
		if ( $methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
		{
			$object->addAttribute('Burnup', '', translate('Burnup'), true);
		}
		
		$object->addAttribute('Indexes', '', translate('Показатели'), true);
		
		$hidden = array ('StartDate', 'FinishDate', 'InitialVelocity', 'Caption', 'Description');
		
		foreach( $hidden as $attribute )
		{
		    $object->setAttributeVisible($attribute, false);
		}
    }
}