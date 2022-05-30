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
		$setting->setSorts( array('Priority','OrderNum') );
        $settings->add( $setting );

        // productbacklog report
        $setting = new ReportSetting('productbacklog');
        $setting->setSorts( array(
        		'Priority',
        		'OrderNum',
                'RecordCreated.D'
        ));
        $setting->setGroup( 'none' );
        $settings->add( $setting );
        
        // board
        $setting = new PageListSetting('RequestBoard');
        $columns = array('UID','Caption','OpenTasks','RecentComment','Fact','Estimation','Attachment','Links','Tags','DeliveryDate');

        if ( !$methodology_it->RequestEstimationUsed() ) {
            $columns[] = 'TasksPlanned';
        }

        $setting->setVisibleColumns($columns);
        $settings->add( $setting );

        $object = getFactory()->getObject('Request');
        $setting = new ReportSetting('issues-trace');
 	    $visible = array_merge( 
            array(
                'UID',
                'Caption',
                'Estimation',
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
            array('UID', 'Caption', 'Author', 'RecordCreated', 'Environment','SubmittedVersion', 'ClosedInVersion', 'TestFound', 'TestExecution')
        );
        $setting->setSorts(array('RecordCreated.D'));
        $settings->add( $setting );

        $setting = new ReportSetting('issuesestimation');
        $columns = array('UID', 'Caption', 'Estimation', 'EstimationLeft', 'Tasks', 'Fact', 'TasksPlanned');
        $taskTypeIt = getFactory()->getObject('TaskTypeUnified')->getAll();
        while( !$taskTypeIt->end() ) {
            $columns[] = 'Planned'.$taskTypeIt->getId();
            $taskTypeIt->moveNext();
        }
        $setting->setVisibleColumns($columns);
        $setting->setSorts(array('RecordCreated.D'));
        $setting->setGroup( $methodology_it->HasPlanning() ? 'Iteration' : 'PlannedRelease' );
        $settings->add( $setting );

        // readyissues
        $setting = new ReportSetting('readyissues');
        $setting->setVisibleColumns(
            array('UID', 'Caption', 'SourceCode', 'Fact', 'FinishDate')
        );
        $setting->setSorts(array('FinishDate.D','ClosedInVersion.D'));
        $setting->setGroup('Type');
        $settings->add( $setting );

        $setting = new ReportSetting('releaseburndown');
        $setting->setVisibleColumns(
            array(
                'UID', 'Caption', 'Owner', 'Priority', 'State', 'RecentComment', 'LeftWork', 'Fact'
            )
        );
        $setting->setGroup('Function.D');
        $setting->setSorts(
            array(
                'State.A',
                'OrderNum.A',
                'Priority.A'
            )
        );
        $settings->add( $setting );

        $setting = new ReportSetting('assignedissues');
        $setting->setVisibleColumns(
            array('UID', 'Caption', 'Fact', 'Priority', 'Deadlines', 'Estimation', 'EstimationLeft')
        );
        $setting->setSorts(array('Priority', 'OrderNum'));
        $setting->setGroup('Owner.A');
        $settings->add( $setting );

    }
}