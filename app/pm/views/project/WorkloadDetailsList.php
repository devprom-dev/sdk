<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/PMDetailsList.php";

class WorkloadDetailsList extends PMDetailsList
{
	private $strategy = null;

	function setupColumns()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Task')) ) {
			$this->strategy = $methodology_it->TaskEstimationUsed() ? new EstimationHoursStrategy() : new EstimationNoneStrategy();
		}
		else {
			$this->strategy = $methodology_it->getEstimationStrategy();
		}

		foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
			if ( $attribute == 'Caption' ) continue;
			$this->getObject()->setAttributeVisible($attribute, false);
		}
		parent::setupColumns();
	}

	function drawCell( $object_it, $attr )
	{
		echo $this->getRenderView()->render('pm/UserWorkloadDetails.php', array (
			'user_id' => $object_it->getId(),
			'user_name' => $object_it->getDisplayName(),
			'data' => $this->workload[$object_it->getId()],
			'measure' => $this->strategy
		));
	}

    function drawCellShort( $object_it, $attr )
    {
        echo $this->getRenderView()->render('pm/UserWorkloadDetails.php', array (
            'user_id' => $object_it->getId(),
            'user_name' => $object_it->getDisplayName(),
            'data' => $this->workload[$object_it->getId()],
            'measure' => $this->strategy,
            'skipPhoto' => 'true'
        ));
    }

    protected function buildWorkItemWorkload()
    {
        $projectIt = getSession()->getProjectIt();
        $workItemIt = getFactory()->getObject('WorkItem')->getRegistry()->Query(
            array(
                new FilterVpdPredicate(),
                new FilterAttributeNullPredicate('FinishDate')
            )
        );
        while( !$workItemIt->end() )
        {
            if ( $workItemIt->get('Assignee') > 0 ) {
                $this->workload[$workItemIt->get('Assignee')]['Planned'] += $workItemIt->get('Planned');
                $this->workload[$workItemIt->get('Assignee')]['LeftWork'] += $workItemIt->get('LeftWork');
            }
            $workItemIt->moveNext();
        }

        $userIds = $workItemIt->fieldToArray('Assignee');
        $iterationIds = array_filter($workItemIt->fieldToArray('Release'), function( $value ) {
            return $value > 0;
        });
        if ( count($iterationIds) < 1 ) {
            $iterationIds = array(-1);
        }
        $iterationIt = getFactory()->getObject('Iteration')->getRegistry()->Query(
            array(
                new FilterInPredicate($iterationIds)
            )
        );
        $releaseIds = array_filter($workItemIt->fieldToArray('PlannedRelease'), function($value) {
            return $value;
        });
        if ( count($releaseIds) < 1 ) {
            $releaseIds = array(-1);
        }
        $releaseIt = getFactory()->getObject('Release')->getRegistry()->Query(
            array(
                new FilterInPredicate($releaseIds)
            )
        );

        foreach( $userIds as $userId ) {
            $this->workload[$userId]['Iterations'] = array();
            $iterationIt->moveFirst();
            while( !$iterationIt->end() )
            {
                $data = $this->getIterationMetrics($iterationIt, $userId, $projectIt);
                if ( $data['leftwork'] < 1 ) {
                    $iterationIt->moveNext();
                    continue;
                }
                $this->workload[$userId]['Iterations'][] = $data;
                $iterationIt->moveNext();
            }

            if ( count($this->workload[$userId]['Iterations']) > 0 ) continue;

            $releaseIt->moveFirst();
            while( !$releaseIt->end() )
            {
                $data = $this->getIterationMetrics($releaseIt, $userId, $projectIt);
                if ( $data['leftwork'] < 1 ) {
                    $releaseIt->moveNext();
                    continue;
                }
                $this->workload[$userId]['Iterations'][] = $data;
                $releaseIt->moveNext();
            }
        }
    }

    protected function getIterationMetrics( $iterationIt, $userId, $projectIt )
    {
        $data = array();
        $data['leftwork'] = $iterationIt->getLeftWorkParticipant( $userId );
        if ( $data['leftwork'] < 1 ) return $data;

        $data['title'] = $iterationIt->getHtmlDecoded('Caption');
        if ( $projectIt->get('VPD') != $iterationIt->get('VPD') ) {
            $data['title'] = '{'.$iterationIt->get('ProjectCodeName').'} '.$data['title'];
        }

        $worker_it = getFactory()->getObject('Participant')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('SystemUser', $userId),
                new FilterVpdPredicate($iterationIt->get('VPD'))
            )
        );
        $data['capacity'] = $iterationIt->getLeftDuration() * $worker_it->get('Capacity');

        $method = new ObjectModifyWebMethod($iterationIt);
        if ( $method->hasAccess() ) {
            $method->setRedirectUrl('donothing');
            $data['url'] = $method->getJSCall();
        }

        return $data;
    }

    function getRenderParms()
    {
        $this->buildWorkItemWorkload();
        return parent::getRenderParms();
    }
}
