<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class ProjectMetricsSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('ProjectMetricsList');
        $setting->setVisibleColumns( array('Metric', 'MetricValue', 'RecordCreated') );
        $settings->add( $setting );
    }
}