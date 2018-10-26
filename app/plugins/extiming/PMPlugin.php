<?php
include "model/ActivityMetadataBuilderEx2Timing.php";

class extimingPM extends PluginPMBase
{
	public function getBuilders()
	{
		return array(
			new ActivityMetadataBuilderEx2Timing()
		);
	}

    function interceptMethodFormCreateFieldObject( & $form, $attr )
    {
        if ( $form instanceof SpentTimeForm && $attr == 'TaskType' ) {
            $taskId = $form->getFieldValue('Task');
            if ( $taskId != '' && $form->getLeftFieldName() == 'LeftWork' ) {
                $taskIt = getFactory()->getObject('Task')->getExact($taskId);
                $form->getObject()->setAttributeDefault('TaskType', $taskIt->get('TaskType'));
            }
        }
    }

    function interceptMethodFormExtendModel( & $form )
    {
        if ( $form instanceof SpentTimeFormEmbedded ) {
            $taskId = $form->getFieldValue('Task');
            if ( $taskId != '' && $form->getLeftWorkAttribute() == 'LeftWork' ) {
                $taskIt = getFactory()->getObject('Task')->getExact($taskId);
                $form->getObject()->setAttributeDefault('TaskType', $taskIt->get('TaskType'));
            }
        }
    }
}