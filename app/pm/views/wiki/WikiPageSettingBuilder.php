<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class WikiPageSettingBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageTableSetting('PMWikiTable');
	    $setting->setSorts( array('DocumentId') );
	    $settings->add( $setting );
    }
}