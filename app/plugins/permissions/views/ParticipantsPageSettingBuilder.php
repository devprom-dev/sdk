<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class ParticipantsPageSettingBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('ParticipantList');
        
        $setting->setGroup( 'ProjectRole' );
        
        if ( getSession()->getProjectIt()->IsProgram() )
        {
            $setting->setVisibleColumns( array('UserAvatar', 'Caption', 'Email', 'Phone', 'Project', 'Capacity', 'ParticipantRole') );
        }
        else
        {
            $setting->setVisibleColumns( array('UserAvatar', 'Caption', 'Email', 'Phone', 'Capacity', 'ParticipantRole') );
        }
        
        $settings->add( $setting );
        
        $setting = new PageTableSetting('ParticipantTable');
        
	    $setting->setSorts( array( 'Caption' ) );
        
        $settings->add( $setting );
    }
}