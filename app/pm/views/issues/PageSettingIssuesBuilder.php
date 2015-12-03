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
        $setting->setFilters( array('release', 'state', 'priority', 'target') );
		$setting->setSorts( $methodology_it->get('IsRequestOrderUsed') == 'Y' ? array('Priority','OrderNum') : array('Priority') );
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
        		'Priority',
        		$methodology_it->get('IsRequestOrderUsed') == 'Y' ? 'OrderNum' : 'Priority',
                'RecordCreated.D'
        ));
        $setting->setGroup( 'none' );
        $setting->setFilters( array('state', 'type', 'priority') );
        $settings->add( $setting );
        
        // board
        $setting = new PageListSetting('RequestBoard');
        $columns = array('UID', 'Caption', 'OpenTasks', 'RecentComment', 'Fact', 'Estimation', 'Attachment', 'Links', 'Tags');
        if ( $methodology_it->get('IsRequestOrderUsed') == 'Y' )
        {
        	//$columns[] = 'OrderNum';
        }
        $setting->setVisibleColumns($columns);
        $settings->add( $setting );

        // tasks-trace report
        $object = getFactory()->getObject('Request');
        $setting = new ReportSetting('issues-trace');
 	    $visible = array_merge( 
 	    		array(
 	    				'UID', 
 	    				'Caption',
 	    				'Function'
 	    		),
                array_filter($object->getAttributesByGroup('trace'), function($value) use ($object) {
                    return $object->IsAttributeVisible($value);
                })
		);
        $setting->setVisibleColumns($visible);
        $settings->add( $setting );
        
        // issuesboardcrossproject
        $setting = new ReportSetting('issuesboardcrossproject');
        $setting->setGroup( 'Project' );
        $setting->setFilters( array('state', 'type', 'priority', 'target') );
        $settings->add( $setting );

        // bugs
        $setting = new ReportSetting('bugs');
        $setting->setVisibleColumns(
            array('UID', 'Caption', 'Author', 'RecordCreated', 'SubmittedVersion', 'ClosedInVersion', 'TestFound', 'TestExecution')
        );
        $setting->setSorts(array('RecordCreated.D'));
        $settings->add( $setting );

        // resolvedissues
        $setting = new ReportSetting('resolvedissues');
        $setting->setVisibleColumns(
            array('UID', 'Caption', 'ClosedInVersion', 'Tasks', 'TestExecution', 'PlannedRelease', 'Priority')
        );
        $setting->setSorts(array('RecordCreated.D'));
        $settings->add( $setting );
    }
}