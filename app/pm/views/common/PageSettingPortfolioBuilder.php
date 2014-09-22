<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingPortfolioBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        // mytasks report
        
        $setting = new ReportSetting('mytasks');
        
        $setting->setSorts( array('Priority') );

        $setting->setFilters( array('taskstate', 'taskassignee', 'target') );
        
        $setting->setVisibleColumns( array('UID', 'Priority', 'Caption', 'State', 'TaskType', 'Project', 'Spent') );
        
        $setting->setSections( array('none') );
        
        $settings->add( $setting );
        
        // myissues report
        
        $setting = new ReportSetting('myissues');
        
        $setting->setSorts( array('Priority') );
        
        $setting->setFilters( array('state', 'owner', 'target') );

        $setting->setSections( array('none') );
        
        $setting->setVisibleColumns( array('UID', 'Priority', 'Caption', 'State', 'Project', 'Spent') );
        
        $settings->add( $setting );

        // product backlog report
        
        $setting = new ReportSetting('productbacklog');
        
        $setting->setSorts( array('Priority') );
        
        $setting->setFilters( array('state', 'owner', 'target') );

        $setting->setVisibleColumns( array('UID', 'Priority', 'Caption', 'State', 'Project', 'RecordCreated') );
        
        $settings->add( $setting );
    }
}