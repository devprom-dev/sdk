<?php
class RequestDueDatesPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();

		 $columns[] =
			 "  t.DeliveryDate DueDate ";
         $columns[] =
         	 "  IFNULL(LEAST(5, YEARWEEK(t.DeliveryDate) - YEARWEEK(IFNULL(t.FinishDate, NOW()))) + 2, 7) DueWeeks ";
         
         return $columns;
     }

     function map( & $parms )
     {
         if ( !array_key_exists('DueWeeks', $parms) ) return;

         if ( $this->getObject()->getAttributeType('LeadTimeSLA') != '' )
         {
             $value_it = getFactory()->getObject('DeadlineSwimlane')->getExact($parms['DueWeeks']);
             if ( $value_it->getId() == '' ) return;
             $parms['Estimation'] = $value_it->get('Days') * 24;
         }
     }

     function modify( $object_id, $parms )
     {
        if ( !array_key_exists('DueWeeks', $parms) ) return;
        $this->setDueDate( $object_id, $parms['DueWeeks'] );
     }

     function add($object_id, $parms)
     {
        if ( !array_key_exists('DueWeeks', $parms) ) return;
        $this->setDueDate( $object_id, $parms['DueWeeks'] );
     }

    protected function setDueDate($object_id, $value)
    {
        $value_it = getFactory()->getObject('DeadlineSwimlane')->getExact($value);
        if ( $value_it->getId() == '' ) return;

        if ( $this->getObject()->getAttributeType('LeadTimeSLA') == '' ) {
            $this->setMilestone( $object_id, $this->getMilestoneIt($value_it->get('ReferenceName')) );
        }
    }

    protected function setMilestone( $object_id, $milestone_it )
    {
        if ( $milestone_it->getId() == '' ) return;

        $trace_it = getFactory()->getObject('RequestTraceMilestone')->getByRef('ChangeRequest', $object_id);
        while( !$trace_it->end() ) {
            $trace_it->object->delete($trace_it->getId());
            $trace_it->moveNext();
        }
        $trace_it->object->add_parms(
            array (
                'ObjectId' => $milestone_it->getId(),
                'ObjectClass' => get_class($milestone_it->object),
                'ChangeRequest' => $object_id
            )
        );
    }

    protected function getMilestoneIt( $date )
    {
        if ( $date == '' ) return getFactory()->getObject('Milestone')->getEmptyIterator();

        $milestone_it = getFactory()->getObject('Milestone')->getByRef('MilestoneDate', $date);
        if ( $milestone_it->getId() == '' ) {
            $milestone_it = $milestone_it->object->getRegistry()->Create(
                array (
                    'MilestoneDate' => $date
                )
            );
        }
        return $milestone_it;
    }
}
