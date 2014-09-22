<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingIssuesBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
        $setting = new PageListSetting('RequestList');
        
        $setting->setGroup( 'none' );
        
        $setting->setVisibleColumns( array('UID', 'Caption', 'State', 'Priority') );
		
        $settings->add( $setting );

        // issues table
        
        $setting = new PageTableSetting('RequestTable');
        
        $setting->setFilters( array('release', 'state', 'type', 'priority') );

		$setting->setSorts( $methodology_it->get('IsRequestOrderUsed') == 'Y' ? array('OrderNum') : array('Priority') );
        
        $settings->add( $setting );

        // myissues report
        
        $setting = new ReportSetting('myissues');
        
        $setting->setFilters( array('state', 'owner') );
        
        $setting->setVisibleColumns( array('UID', 'Caption', 'State', 'Priority', 'Spent') );
        
        $setting->setSections( array('none') );

        $setting->setGroup( 'none' );
        
        $settings->add( $setting );

        // newissues report
        
        $setting = new ReportSetting('newissues');
        
        $setting->setSorts( array('RecordCreated.D') );
        
        $setting->setFilters( array('state', 'type', 'priority', 'submittedon') );
        
        $setting->setVisibleColumns( array('UID', 'Caption', 'Priority', 'RecordCreated') );
        
        $settings->add( $setting );

        // resolvedissues report
        
        $setting = new ReportSetting('resolvedissues');
        
        $setting->setSorts( array('RecordCreated.D') );
        
        $setting->setFilters( array('release', 'state', 'type', 'version') );
        
        $settings->add( $setting );

        // productbacklog report
        
        $setting = new ReportSetting('productbacklog');
        
        $setting->setSorts( array(
                $methodology_it->get('IsRequestOrderUsed') == 'Y' ? 'OrderNum' : 'Priority',
                'RecordCreated.D'
        ));
        
        $setting->setGroup( 'none' );

        $setting->setFilters( array('state', 'type', 'priority') );
        
        $settings->add( $setting );
        
        // board
        $setting = new PageListSetting('RequestBoard');
        
        $setting->setVisibleColumns( array('UID', 'Caption', 'Footer', 'Tasks', 'RecentComment', 'Fact', 'Estimation', 'Attachment') );
		
        $settings->add( $setting );

        
        // tasks-trace report
        
        $setting = new ReportSetting('issues-trace');
        
 	    $visible = array_merge( 
 	    		array(
 	    				'UID', 
 	    				'Caption',
 	    				'Function'
 	    		), 
		    	getFactory()->getObject('Request')->getAttributesByGroup('trace')
		);
        
        $setting->setVisibleColumns($visible);
        
        $settings->add( $setting );
        
        // issuesboardcrossproject
        $setting = new ReportSetting('issuesboardcrossproject');
        
        $setting->setGroup( 'Project' );

        $setting->setFilters( array('state', 'type', 'priority', 'target') );
        
        $settings->add( $setting );
    }
}