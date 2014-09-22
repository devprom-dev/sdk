<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class WikiTemplateSettingBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('WikiTemplateList');
        
        $setting->setGroup( 'none' );
        
        $setting->setVisibleColumns( array('Content') );
        
        $settings->add( $setting );
        
        $setting = new PageTableSetting('WikiTemplateTable');
        
	    $setting->setSorts( array('none') );
        
        $settings->add( $setting );
    }
}