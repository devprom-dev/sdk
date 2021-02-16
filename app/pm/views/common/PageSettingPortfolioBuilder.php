<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingPortfolioBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        // product backlog report
        $setting = new ReportSetting('productbacklog');
        $setting->setSorts( array('Priority') );
        $setting->setVisibleColumns( array('UID', 'Priority', 'Caption', 'State', 'Project', 'RecordCreated') );
        $settings->add( $setting );
    }
}