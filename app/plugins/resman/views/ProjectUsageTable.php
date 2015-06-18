<?php

include "ProjectUsageList.php";

class ProjectUsageTable extends PMPageTable
{
    function getObject()
    {
        return getFactory()->getObject('HumanResource');
    }

    function getList()
    {
        $method = new ResourceFilterScaleWebMethod();
        $method->setFilter( $this->getFiltersName() );

        $scale = $method->getValue();
        if ( $scale == '' )
        {
            $scale = 'month';
        }

        return new ProjectUsageList( $this->getObject(), $scale );
    }

    function getFilters()
    {
        $filters = array(
                new ResourceFilterFormatWebMethod(),
                new ResourceFilterViewWebMethod(),
                new ResourceFilterScaleWebMethod(),
                new ResourceFilterYearWebMethod(),
                new ResourceFilterMonthWebMethod()
        );

        return array_merge( $filters, parent::getFilters() );
    }

    function drawFooter()
    {
        if ( !$this->getListRef()->HasRows() )
        {
            return;
        }

        $values = $this->getFilterValues();
        switch ( $values['format'] )
        {
            default:
                echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
                echo ' - '.text('ee39');
                echo '</div>';
                echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
                echo '<div class="progress_bar_frame" style="width:12px;height:12px;">';
                echo '<div class="progress_bar" style="background:red;width:100%;height:12px;">&nbsp;</div>';
                echo '</div>';
                echo '</div>';

                echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
                echo ' - '.text('ee40');
                echo '</div>';
                echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
                echo '<div class="progress_bar_frame" style="width:12px;height:12px;">';
                echo '<div class="progress_bar" style="background:#EBE614;width:100%;height:12px;">&nbsp;</div>';
                echo '</div>';
                echo '</div>';

                echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
                echo ' - '.text('ee41');
                echo '</div>';
                echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
                echo '<div class="progress_bar_frame" style="width:12px;height:12px;">';
                echo '<div class="progress_bar" style="width:100%;height:12px;">&nbsp;</div>';
                echo '</div>';
                echo '</div>';
        }
    }

    function getNewActions()
    {
        return array();
    }
}
