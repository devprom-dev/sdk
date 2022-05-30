<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/MetricIssueBuilder.php";
include_once SERVER_ROOT_PATH . "pm/classes/issues/persisters/RequestMetricsFactPersister.php";

class MetricIssueBuilderFact extends MetricIssueBuilder
{
    public function buildAll( $registry, $queryParms )
    {
        $issue_it = $registry->Query(
            array_merge($queryParms, array(
                new \RequestHasTasksPredicate(),
                new \RequestMetricsFactPersister()
            ))
        );

        while( !$issue_it->end() ) {
            list($total, $tasks) = preg_split('/:/', $issue_it->get('MetricSpentHoursData'));

            if ( !is_array($tasks) ) $tasks = array();
            $tasks = join(',',
                array_filter(array_unique($tasks), function($value){
                    return $value > 0;
                })
            );

            if ( $issue_it->get('Fact') != $total || $issue_it->get('FactTasks') != $tasks ) {
                DAL::Instance()->Query(
                    " UPDATE pm_ChangeRequest 
                         SET Fact = {$total}, 
                             FactTasks = '{$tasks}',
                             FactToday = '{$issue_it->get('MetricSpentHoursToday')}'
                       WHERE pm_ChangeRequestId = {$issue_it->getId()} "
                );
            }
            $issue_it->moveNext();
        }
    }
}