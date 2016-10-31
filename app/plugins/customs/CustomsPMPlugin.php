<?php

include_once "reports/ReportsCustomsBuilder.php";

class CustomsPMPlugin extends PluginPMBase
{
    function getModules()
    {
        return array (
            'workflowanalysis' =>
                array(
                    'includes' => array( 'customs/reports/workflowanalysis/ReportWorkflowAnalysisPage.php' ),
                    'classname' => 'ReportWorkflowAnalysisPage',
                    'title' => text('customs1'),
                    'AccessEntityReferenceName' => 'pm_ChangeRequest',
                    'area' => FUNC_AREA_MANAGEMENT,
                    'icon' => 'icon-signal'
                )
        );
    }

    function getBuilders()
    {
        return array (
        		new ReportsCustomsBuilder()
        );
    }
}