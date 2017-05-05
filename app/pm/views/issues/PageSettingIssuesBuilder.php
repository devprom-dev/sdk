<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingIssuesBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $integrationsCount = getFactory()->getObject('Integration')->getRecordCount();
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
        $setting = new PageListSetting('RequestList');
        $setting->setGroup( 'none' );
        $setting->setVisibleColumns( array('UID', 'Caption', 'State', 'Priority') );
        $settings->add( $setting );

        // issues table
        $setting = new PageTableSetting('RequestTable');
        $setting->setFilters( array('release', 'state', 'priority', 'target') );
		$setting->setSorts( array('Priority','OrderNum') );
        $settings->add( $setting );

        // newissues report
        $setting = new ReportSetting('newissues');
        $setting->setSorts( array('RecordCreated.D') );
        $setting->setFilters( array('state', 'type', 'priority', 'submittedon') );
        $setting->setVisibleColumns( array('UID', 'Caption', 'Priority', 'RecordCreated') );
        $settings->add( $setting );

        // productbacklog report
        $setting = new ReportSetting('productbacklog');
        $setting->setSorts( array(
        		'Priority',
        		'OrderNum',
                'RecordCreated.D'
        ));
        $setting->setGroup( 'none' );
        $setting->setFilters( array('state', 'type', 'priority') );
        $settings->add( $setting );
        
        // board
        $setting = new PageListSetting('RequestBoard');
        $columns = array('UID','Caption','OpenTasks','RecentComment','Fact','Estimation','Attachment','Links','Tags');
        $setting->setVisibleColumns($columns);
        $settings->add( $setting );

        // tasks-trace report
        $object = getFactory()->getObject('Request');
        $setting = new ReportSetting('issues-trace');
 	    $visible = array_merge( 
            array(
                'UID',
                'Caption',
                'State',
                'Function',
                'OpenTasks',
                'Fact'
            ),
            array_filter($object->getAttributesByGroup('trace'), function($value) use ($object) {
                return $object->IsAttributeVisible($value) && !in_array($value, array('TestFound'));
            }),
            $integrationsCount > 0 ? array('IntegrationLink') : array()
		);
        $setting->setVisibleColumns($visible);
        $setting->setGroup('Function');
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
            array('UID', 'Caption', 'ClosedInVersion', 'Tasks', 'TestExecution', 'PlannedRelease', 'Priority', 'Estimation', 'Fact')
        );
        $setting->setSorts(array('RecordCreated.D'));
        $settings->add( $setting );

        $setting = new ReportSetting('issuesestimation');
        $setting->setVisibleColumns(
            array('UID', 'Caption', 'Estimation', 'Tasks', 'Fact', 'Priority')
        );
        $setting->setSorts(array('RecordCreated.D'));
        $setting->setGroup( $methodology_it->HasPlanning() ? 'Iteration' : 'PlannedRelease' );
        $settings->add( $setting );
    }
}