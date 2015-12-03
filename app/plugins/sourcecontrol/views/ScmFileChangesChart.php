<?php
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartSingleTimelineWidget.php";

class ScmFileChangesChart extends PMPageChart
{
    function getGroupFields()
    {
        return array_merge(PageList::getGroupFields(), array('Author'));
    }

    function getChartWidget()
    {
        $widget = new FlotChartSingleTimelineWidget();
        $widget->setLabel(text('sourcecontrol49'));
        return $widget;
    }

    function getTableVisible() {
        return false;
    }
}
