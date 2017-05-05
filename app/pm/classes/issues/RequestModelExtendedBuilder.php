<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/RequestSpentTimePersister.php";
include_once "persisters/RequestQuestionsPersister.php";

class RequestModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

   		if ( $methodology_it->IsTimeTracking() && $object->getAttributeType('Fact') != '' ) {
			$object->addAttribute( 'Spent', 'REF_ActivityRequestId', translate('Списание времени'), false );
		    $object->addPersister( new RequestSpentTimePersister(array('Spent')) );
		}
		$object->addPersister( new RequestQuestionsPersister(array('Question')) );

        $this->removeAttributes( $object, $methodology_it );
	}

    private function removeAttributes( $object, $methodology_it )
    {
        if ( $methodology_it->getId() > 0 && !$methodology_it->RequestEstimationUsed() ) {
            $object->removeAttribute( 'Estimation' );
        }

        if ( $methodology_it->getId() > 0 && !$methodology_it->HasFeatures() ) {
            $object->addAttributeGroup('Function', 'system');
        }

        if ( $methodology_it->getId() > 0 && !$methodology_it->HasReleases() ) {
            $object->removeAttribute( 'PlannedRelease' );
        }

        $strategy = $methodology_it->getEstimationStrategy();

        if ( $methodology_it->getId() > 0 && !$strategy->hasEstimationValue() ) {
            $object->removeAttribute( 'Estimation' );
            $object->removeAttribute( 'EstimationLeft' );
        }

        if ( ! $strategy instanceof EstimationHoursStrategy ) {
            $object->removeAttribute( 'EstimationLeft' );
        }

        if ( !$methodology_it->IsTimeTracking() )
        {
            $object->removeAttribute('Fact');
            $object->removeAttribute('FactTasks');
        }
    }
}