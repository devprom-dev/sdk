<?php

class EstimationProxy
{
    static function getEstimatedFinish( $object_it, $formatted = true, $format = '' )
    {
        if ( $format == '' ) $format = getSession()->getLanguage()->getDateFormat();

        $project_it = $object_it->getRef('Project');
        $finish_date = "'".$object_it->get_native('FinishDate')."'";

        if ( $object_it->getRef('Project')->getMethodologyIt()->IsAgile() ) {
            // get real duration based on left work and actual velocity
            $velocity = $object_it->get('InitialVelocity');
            list( $capacity, $maximum, $actual_velocity, $estimation ) = $object_it->getRealBurndownMetrics();

            $estimatedDuration = $velocity > 0 ? $estimation / $velocity : 0;

            $left_days = round( max(
                max($estimatedDuration, $object_it->get('LeftDurationInWorkingDays')),
                $object_it->get('UncompletedItems') > 0 ? 1 : 0
            ), 0);

            if ( $left_days > 0 ) {
                $predicate = $project_it->getDaysInWeek() < 6
                    ? " AND i.StartDateWeekday NOT IN (1,7) " : ($project_it->getDaysInWeek() < 7 ? " AND i.StartDateWeekday <> 1 " : "");

                $finish_date =
                    " SELECT MAX(FinishDate) FROM ( 
                    SELECT i.FinishDate FROM pm_CalendarInterval i 
                     WHERE i.StartDateOnly >= GREATEST(CURDATE(),'".$object_it->get_native('AdjustedStart')."') AND i.Kind = 'day' ".$predicate."
                  ORDER BY i.StartDateOnly ASC LIMIT ".$left_days." 
                  ) t";
            }
        }

        $it = $object_it->object->createSQLIterator(
            " SELECT GREATEST((".$finish_date."), '".$object_it->get_native('FinishDate')."', ".$object_it->getWorkItemsMaxDateQuery().") EstDate "
        );
        $finish_date = $it->get('EstDate');

        if ( $formatted ) {
            return getSession()->getLanguage()->getDateUserFormatted( $finish_date, $format );
        }
        else {
            $mapper = new ModelDataTypeMappingDate();
            return $mapper->map($finish_date);
        }
    }
}