<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class AttachmentsPageSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('AttachmentsList');
        $setting->setVisibleColumns( array('File', 'FileSize', 'ObjectId', 'RecordCreated') );
        $settings->add( $setting );

        $setting = new PageTableSetting('AttachmentsTable');
		$setting->setSorts( array('RecordCreated.D') );
        $settings->add( $setting );
   }
}