<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class KnowledgeBaseDocumentSettingBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('KnowledgeBaseDocumentList');
        
        $setting->setGroup( 'none' );
        
        $setting->setVisibleColumns( array('Content') );
        
        $settings->add( $setting );
        
        $setting = new PageTableSetting('KnowledgeBaseDocument');
        
	    $setting->setSorts( array('none') );
        
        $settings->add( $setting );
    }
}