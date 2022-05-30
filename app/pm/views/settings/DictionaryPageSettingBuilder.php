<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class DictionaryPageSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('FormFieldList');
        $setting->setGroup( 'Entity' );
        $setting->setVisibleColumns( array('ReferenceName', 'Transition', 'State') );
        $settings->add( $setting );
    }
}