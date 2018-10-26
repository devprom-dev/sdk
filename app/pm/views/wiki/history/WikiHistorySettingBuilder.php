<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class WikiHistorySettingBuilder extends PageSettingBuilder
{
	private $page_it = null;
	
	function __construct( $page_it )
	{
		$this->page_it = $page_it;
	}
	
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('WikiHistoryList');
        $setting->setGroup( 'ChangeDate' );
        $columns = array('UserAvatar', 'Content', 'RecordModified');
        $setting->setVisibleColumns( $columns );
        $settings->add( $setting );
        
        $setting = new PageTableSetting('WikiHistoryTable');
	    $setting->setFilters( array('formatting', 'action', 'participant', 'start') );
	    $setting->setSorts( array('RecordModified.D') );
        $setting->setRowsNumber(5);
        $settings->add( $setting );
    }
}