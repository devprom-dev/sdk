<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/MetricIssueBuilder.php";
include_once SERVER_ROOT_PATH . "pm/classes/issues/persisters/RequestMetricsDeliveryDatePersister.php";

class MetricIssueBuilderDeliveryDate extends MetricIssueBuilder
{
    public function buildAll( $registry, $queryParms )
    {
        $this->processIssues(
            $registry->Query(
                array_merge($queryParms, array(
                    new \StatePredicate('N,I'),
                    new \RequestMetricsDeliveryDatePersister()
                ))
            )
        );

        $this->processIssues(
            $registry->Query(
                array_merge($queryParms, array(
                    new \StatePredicate('N,I'),
                    new \RequestDependencyFilter('duplicates,implemented,blocked'),
                    new \RequestMetricsDeliveryDatePersister()
                ))
            )
        );
    }

    private function processIssues( $issue_it )
    {
        while( !$issue_it->end() ) {
            if ( $issue_it->get('MetricDeliveryDate') != $issue_it->get('DeliveryDate') ) {
                $newDate = $issue_it->get('MetricDeliveryDate') != ''
                    ? "'{$issue_it->get('MetricDeliveryDate')}'"
                    : 'NULL';
                $newMethod = $issue_it->get('MetricDeliveryDateMethod') != ''
                    ? $issue_it->get('MetricDeliveryDateMethod')
                    : '0';

                DAL::Instance()->Query(
                    " UPDATE pm_ChangeRequest 
                         SET DeliveryDate = {$newDate}, 
                             DeliveryDateMethod = '{$newMethod}' 
                       WHERE pm_ChangeRequestId = {$issue_it->getId()}"
                );
            }
            $issue_it->moveNext();
        }
    }
}