<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class StageModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Stage ) return;
    	
    	$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$object->addAttribute('Stage', '', translate('������ �������'), true, true, '', 10);
		$object->addAttribute('Deadlines', '', translate('�����'), true);
		$object->addAttribute('EstimatedStartDate', 'DATETIME', translate('������ ������'), false, false);
		$object->addAttribute('EstimatedFinishDate', 'DATETIME', translate('������ ���������'), false, false);
		$object->addAttribute('ActualStartDate', 'DATE', translate('������ �� �����'), false, true);
		$object->addAttribute('ActualFinishDate', 'DATE', translate('��������� �� �����'), false, true);
		$object->addAttribute('Burndown', '', translate('Burndown'), true);
		
		if ( $methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
		{
			$object->addAttribute('Burnup', '', translate('Burnup'), true);
		}
		
		$object->addAttribute('Indexes', '', translate('����������'), true);
		
		$hidden = array ('StartDate', 'FinishDate', 'InitialVelocity', 'Caption', 'Description');
		foreach( $hidden as $attribute )
		{
		    $object->setAttributeVisible($attribute, false);
		}
    }
}