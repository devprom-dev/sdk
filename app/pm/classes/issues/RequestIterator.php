<?php
include_once SERVER_ROOT_PATH . "pm/classes/workflow/StatableIterator.php";

class RequestIterator extends StatableIterator
{
	function getDisplayName() {
	    if ( $this->getId() == '' ) return parent::getDisplayName();
	    return $this->getObjectDisplayName() . ': ' . parent::getDisplayName();
	}

	function getDisplayNameExt( $prefix = '' )
    {
        $priorityColor = parent::get('PriorityColor');
        if ( $priorityColor != '' ) {
            $prefix .= '<span class="pri-cir" style="color:'.$priorityColor.'">&#x25cf;</span>';
        }

        if ( $this->get('Deadlines') != '' && $this->get('DueWeeks') > -3 && $this->get('DueWeeks') < 4 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'" title="'.$this->object->getAttributeUserName('DeliveryDate').'">';
            $prefix .= $this->getDateFormattedShort('DeliveryDate');
            $prefix .= '</span> ';
        }

        $displayAttributes = array();
        foreach( $this->object->getAttributesByGroup('display-name') as $attribute ) {
            if ( $this->get($attribute) == '' ) continue;
            if ( in_array($attribute, array('Estimation')) ) {
                $displayAttributes[] = '<span class="label label-success">'.
                    $this->getRef('Project')->getMethodologyIt()->getEstimationStrategy()->getDimensionText($this->get($attribute)).
                    '</span>';
            }
            elseif ( in_array($attribute, array('TasksPlanned')) ) {
                $displayAttributes[] = '<span class="label label-success">'.
                    $this->getRef('Project')->getMethodologyIt()->getIterationEstimationStrategy()->getDimensionText($this->get($attribute)).
                    '</span>';
            }
            else {
                $displayAttributes[] = '['.$this->get($attribute).']';
            }
        }
        if ( count($displayAttributes) > 0 ) {
            $prefix = $prefix . join(' ', $displayAttributes) . ' ';
        }

        $title = parent::getDisplayNameExt($prefix);

        if ( $this->get('ClosedInVersion') != '' ) {
            $title = ' <span class="badge badge-uid badge-inverse">'.$this->get('ClosedInVersionText').'</span> ' . $title;
        }

        if ( $this->get('TagNames') != '' ) {
            $tags = array_map(function($value) {
                return ' <span class="label label-info label-tag">'.$value.'</span> ';
            }, preg_split('/,/', $this->get('TagNames')));
            $title = join('',$tags) . $title;
        }

        if ( $this->get('TypeReferenceName') != '' ) {
            $title = '<i class="issue '.$this->get('TypeReferenceName').'"></i> '.$title;
        }

        return $title;
    }

    function getObjectDisplayName() {
        return $this->get('TypeName') != '' ? $this->get('TypeName') : parent::getObjectDisplayName();
    }

 	function IsFinished() {
 		return $this->get('FinishDate') != '';
 	}

	function IsTransitable()
	{
		return true;
	}
 	
	function getImplementationIds()
	{
		$result = array();
		$items = preg_split('/,/', $this->get('LinksWithTypes'));
		foreach( $items as $item ) {
			list($title, $id, $link_type) = preg_split('/:/', $item);
			if ( $link_type == 'implemented' ) {
				$result[] = $id;
			}
		}
		return $result;
	}


	/*
	 *  Returns the planned duration of all tasks related to the issue 
	 */ 	
 	function getPlannedDuration()
 	{
 		$duration = 0;
		if ( $this->object->getAttributeType('Tasks') == '' ) return $duration;

 		$task_it = $this->getRef('Tasks');
 		while ( !$task_it->end() && $task_it->get('ChangeRequest') == $this->getId() ) {
			$duration += $task_it->get("Planned");
 			$task_it->moveNext();
 		}	
 		return $duration;
 	} 	

 	function getProgress()
 	{
 		$ids = array();
 		while ( !$this->end() ) {
 			array_push($ids, $this->getId());
 			$this->moveNext();
 		}
 		if ( count($ids) < 1 ) {
 		    return array(
 		        'R' => array(0, 0)
            );
        }

        $total = $this->object->getRegistry()->Count(
            array(
                new FilterInPredicate($ids)
            )
        );
        $resolved = $this->object->getRegistry()->Count(
            array(
                new StatePredicate('terminal'),
                new FilterInPredicate($ids)
            )
        );
        return array(
            'R' => array($total, $resolved)
        );
 	}

 	function getSpecifiedIt()
    {
        if ( $this->count() > 0 && $this->get('Type') == '' && !$this->object instanceof Issue && getSession()->IsRDD() ) {
            return getFactory()->getObject('Issue')->createCachedIterator(
                array($this->getData())
            );
        }
        return $this;
    }
}