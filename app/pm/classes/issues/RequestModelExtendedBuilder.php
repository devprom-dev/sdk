<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/RequestSpentTimePersister.php";
include_once "persisters/IssueUsedByPersister.php";
include "persisters/RequestTasksDetailPersister.php";

class RequestModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof Request) return;
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

   		if ( $methodology_it->IsTimeTracking() && $object->getAttributeType('Fact') != '' ) {
			$object->addAttribute( 'Spent', 'REF_ActivityRequestId', translate('Списание времени'), false );
            $object->addAttributeGroup('Spent', 'trace');
            $object->addPersister( new RequestSpentTimePersister(array('Spent')) );
		}

        if ( $methodology_it->HasTasks() && $methodology_it->TaskEstimationUsed() ) {
            $object->addAttribute( 'TasksPlanned', 'FLOAT', text(2532), false, false, '', 81);
            $object->setAttributeEditable('TasksPlanned', false);
            $object->addAttributeGroup('TasksPlanned', 'workload');
            $object->addAttributeGroup('TasksPlanned', 'hours');
            $object->addAttributeGroup('TasksPlanned', 'additional');
            if ( !$methodology_it->RequestEstimationUsed() ) {
                $object->addAttributeGroup('TasksPlanned', 'display-name');
            }
            $object->addAttribute( 'TasksLeft', 'FLOAT', text(3125), false, false, '', 34);
            $object->setAttributeEditable('TasksLeft', false);
            $object->addAttributeGroup('TasksLeft', 'hours');
            $object->addPersister( new RequestTasksDetailPersister() );
        }
        $this->removeAttributes( $object, $methodology_it );

        $object->addAttribute( 'ProjectPage', 'REF_ProjectPageId', translate('База знаний'), false);
        $object->addAttributeGroup('ProjectPage', 'trace');
        $object->addAttributeGroup('ProjectPage', 'non-form');
        $object->setAttributeEditable('ProjectPage', false);
        $object->addPersister( new IssueUsedByPersister() );

        foreach ( array('Spent') as $attribute ) {
            $object->addAttributeGroup($attribute, 'workload');
            $object->addAttributeGroup($attribute, 'hours');
        }
	}

    private function removeAttributes( $object, $methodology_it )
    {
        $strategy = $methodology_it->getEstimationStrategy();
        if ( $methodology_it->getId() > 0 && $methodology_it->RequestEstimationUsed() && !$strategy->hasEstimationValue() ) {
            $object->removeAttribute( 'EstimationLeft' );
        }

        if ( ! $strategy instanceof EstimationHoursStrategy ) {
            $object->removeAttribute( 'EstimationLeft' );
        }

        $activity = getFactory()->getObject('Activity');
        if ( !$methodology_it->IsTimeTracking() || !getFactory()->getAccessPolicy()->can_read($activity) )
        {
            $object->removeAttribute('Fact');
            $object->removeAttribute('FactToday');
            $object->removeAttribute('Spent');
            $object->removeAttribute('FactTasks');
        }

        $task = getFactory()->getObject('Task');
        if ( !getFactory()->getAccessPolicy()->can_read($task) ) {
            $object->removeAttribute('TasksPlanned');
            $object->removeAttribute('Tasks');
            $object->removeAttribute('OpenTasks');
        }
        if ( !getFactory()->getAccessPolicy()->can_read_attribute($task, 'Planned') ) {
            $object->removeAttribute('TasksPlanned');
        }
    }
}