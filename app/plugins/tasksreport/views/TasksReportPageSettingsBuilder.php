<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class TasksReportPageSettingsBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('TasksReportList');
        $setting->setGroup( 'none' );
        $setting->setVisibleColumns( array(
            'UID', 'Caption', 'TaskType', 'Fact', CONTRACT_REFNAME, 'LastDate', 'StartDateOnly', 'DayFact', 'FactRegion', 'regionCaption'
        ));
        $settings->add( $setting );

        $setting = new PageTableSetting('TasksReportTable');
		$setting->setSorts( array('FinishDate') );
        $settings->add( $setting );
    }
}