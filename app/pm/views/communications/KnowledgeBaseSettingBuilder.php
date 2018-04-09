<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class KnowledgeBaseSettingBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new ReportSetting('knowledgebaselist');
        $setting->setGroup( 'RecordModified.D' );
        $setting->setVisibleColumns( array('UID','Caption','Content','Author', 'RecentComment') );
        $setting->setSorts(
            array(
                'RecordModified.D'
            )
        );
        $settings->add( $setting );
    }
}