<?php
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartSingleTimelineWidget.php";

class ScmProductivityChart extends PMPageChart
{
    function getGroupFields()
    {
        return array_merge(PageList::getGroupFields(), array('Author'));
    }

    function getChartWidget()
    {
        $widget = new FlotChartSingleTimelineWidget();
        $widget->setLabel(text('sourcecontrol51'));
        return $widget;
    }

    function getTableVisible() {
        return false;
    }
}
