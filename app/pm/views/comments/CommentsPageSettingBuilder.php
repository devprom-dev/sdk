<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class CommentsPageSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('CommentsList');
        $setting->setVisibleColumns(array('Caption'));
        $settings->add( $setting );
    }
}