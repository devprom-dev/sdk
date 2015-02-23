<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class WikiDocumentSettingBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('PMWikiDocumentList');
        $setting->setGroup( 'none' );
        $setting->setVisibleColumns( array('Content') );
        $settings->add( $setting );
        
        $setting = new PageTableSetting('PMWikiDocument');
	    $setting->setSorts( array('none') );
	    $setting->setFilters( array('state', 'numbering', 'linkstate', 'baseline') );
	    $settings->add( $setting );
    }
}