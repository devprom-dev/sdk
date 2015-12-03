<?php

include_once SERVER_ROOT_PATH."core/views/charts/FlotChartSingleTimelineWidget.php";

class ProjectMetricsChart extends PMPageChart
{
    function getGroupFields()
    {
        return array_merge(PageList::getGroupFields(), array());
    }

    function getChartWidget()
    {
        $widget = new FlotChartSingleTimelineWidget();
        $widget->setLabel(translate('Значение'));
        return $widget;
    }

    function getTableVisible() {
        return false;
    }
}
