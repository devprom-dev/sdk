<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsSourceControlBuilder extends ReportsBuilder
{
    private $session = null;

    function __construct( $session ) {
        $this->session = $session;
    }

    public function build( ReportRegistry & $object )
    {
        if ( $this->session->getProjectIt()->getMethodologyIt()->get('IsSubversionUsed') != 'Y' ) return;

        $module = getFactory()->getObject('Module');

        $module_it = $module->getExact('sourcecontrol/revision');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array ( 
                    'name' => 'scm-revisions', 
                    'description' => text('sourcecontrol6'),
                    'module' => $module_it->getId(), 
                    'category' => ModuleCategoryBuilderCode::AREA_UID 
            ));
            $object->addReport( array (
                    'name' => 'scm-commitsperauthors',
                    'title' => text('sourcecontrol23'),
                    'description' => text('sourcecontrol31'),
                    'module' => $module_it->getId(),
                    'query' => 'aggregator=none&aggby=Author&group=Repository&view=chart',
                    'category' => ModuleCategoryBuilderCode::AREA_UID,
                    'type' => 'chart' 
            ));
            $object->addReport( array (
                    'name' => 'commitsfreqperauthors',
                    'title' => text('sourcecontrol24'),
                    'description' => text('sourcecontrol32'),
                    'module' => $module_it->getId(),
                    'query' => 'aggregator=COUNT&aggby=Author&group=history&view=chart',
                    'category' => ModuleCategoryBuilderCode::AREA_UID,
                    'type' => 'chart'
            ));
        }

        $module_it = $module->getExact('sourcecontrol/files');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array (
                    'name' => 'scm-files',
                    'description' => text('sourcecontrol30'),
                    'module' => $module_it->getId(),
                    'category' => ModuleCategoryBuilderCode::AREA_UID
            ));
        }

        $module_it = $module->getExact('sourcecontrol/changes');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
            $object->addReport(array(
                'name' => 'scm-filechanges',
                'description' => text('sourcecontrol48'),
                'module' => $module_it->getId(),
                'category' => ModuleCategoryBuilderCode::AREA_UID,
                'query' => 'aggregator=SUM&aggby=Modified&group=DayDate&start=last-month&view=chart',
                'type' => 'chart'
            ));
            $object->addReport(array(
                'name' => 'codeproductivity',
                'title' => text('sourcecontrol52'),
                'module' => $module_it->getId(),
                'category' => ModuleCategoryBuilderCode::AREA_UID,
                'query' => 'aggregator=SUM&aggby=ModifiedPerHour&group=DayDate&start=last-month&view=chart',
                'type' => 'chart'
            ));
        }
    }
}