<?php
include "model/TasksReportRegistry.php";

class tasksreportPM extends PluginPMBase
{
    function getModules()
    {
        return array (
            'tasksreport' =>
                array(
                    'includes' => array( 'tasksreport/views/TasksReportPage.php' ),
                    'classname' => 'TasksReportPage',
                    'title' => 'Затраты по задачам в разрезе регионов',
                    'AccessEntityReferenceName' => 'pm_Task',
                    'area' => FUNC_AREA_MANAGEMENT,
                    'icon' => 'icon-signal'
                )
        );
    }
}