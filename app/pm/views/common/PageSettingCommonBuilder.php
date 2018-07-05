<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingCommonBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $this->buildTaskRelatedUI( $settings );
        
        $this->buildWikiRelatedUI( $settings );
    }
    
    protected function buildTaskRelatedUI( PageSettingSet & $settings )
    {
        // tasks list
        $integrationsCount = getFactory()->getObject('Integration')->getRecordCount();
        
        $setting = new PageListSetting('TaskList');
        $setting->setGroup( 'Release' );
        
        $visible_attributes = array('UID', 'Caption', 'State', 'Assignee', 'TaskType', 'ChangeRequest');
	    $visible_attributes[] = 'Priority';
		
		$setting->setVisibleColumns( $visible_attributes );
        
        $settings->add( $setting );

        // mytasks report
        $visible = array_merge(
            array (
                'Spent',
                'IssueTraces',
                'RecentComment',
                'DueDate'
            ),
            array_filter( $visible_attributes, function($value) {
                return !in_array($value, array('Assignee','ChangeRequest','TaskType'));
            })
        );

        foreach( array('mytasks', 'nearesttasks', 'assignedtasks', 'newtasks', 'issuesmine', 'watchedtasks') as $reportName ) {
            $setting = new ReportSetting($reportName);
            $setting->setVisibleColumns($visible);
            if ( $reportName == 'nearesttasks' ) {
                $setting->setSorts(
                    array(
                        'DueDate',
                        'Priority'
                    )
                );
            }
            $settings->add( $setting );
        }

        // currenttasks report
        $setting = new ReportSetting('currenttasks');
        $setting->setGroup('ChangeRequest');
        $setting->setVisibleColumns(
   				array_filter( $visible_attributes, function($value)
   				{
   						return !in_array($value, array('ChangeRequest')); 
   				})
        );
        $settings->add( $setting );
        
        // resolvedtasks report
        $setting = new ReportSetting('resolvedtasks');
        $setting->setGroup('ChangeRequest');
        $setting->setVisibleColumns(
            array_merge(
   				array_filter( $visible_attributes, function($value)
   				{
   						return !in_array($value, array('ChangeRequest')); 
   				}),
                array(
                    'Planned', 'Fact'
                )
            )
        );
        $settings->add( $setting );

        // tasks-trace report
        $object = getFactory()->getObject('Task');
 	    $visible = array_merge( 
 	    		array(
                    'UID',
                    'Caption',
                    'State',
                    'Fact'
 	    		),
                array_filter($object->getAttributesByGroup('trace'), function($value) use($object) {
                    return $object->IsAttributeVisible($value) && !in_array($value, array('Watchers'));
                })
		);

        $setting = new ReportSetting('tasks-trace');
        $setting->setVisibleColumns(
            array_merge( $visible,
                $integrationsCount > 0
                    ? array (
                        'IntegrationLink'
                    )
                    : array()
            )
        );
        $setting->setGroup('ChangeRequest');
        $settings->add( $setting );

        $setting = new ReportSetting('iterationburndown');
        $setting->setVisibleColumns(
            array(
                'UID', 'Caption', 'TaskType', 'Assignee', 'Priority', 'ChangeRequest', 'State', 'RecentComment', 'LeftWork'
            )
        );
        $setting->setGroup('State.D');
        $setting->setSorts(
            array(
                'OrderNum.A',
                'Priority.A'
            )
        );
        $setting->setFilters(
            array(
                'iteration', 'tasktype', 'assignee'
            )
        );
        $settings->add( $setting );

        $setting = new ReportSetting('tasksplanbyfact');
        $setting->setVisibleColumns(
            array(
                'UID', 'Caption', 'TaskType', 'Assignee', 'Planned', 'Fact'
            )
        );
        $settings->add( $setting );

        // tasks table
        
        $setting = new PageListSetting('TaskBoardList');
        $columns = array('UID', 'Caption', 'RecentComment', 'Fact', 'Attachment', 'Assignee', 'Progress');
        $setting->setVisibleColumns($columns);
        $settings->add( $setting );

        $setting = new PageTableSetting('TaskTable');
        $setting->setFilters( array('release', 'iteration', 'state', 'tasktype', 'taskassignee', 'target') );
	    $setting->setSorts( array('Priority','OrderNum') );
        $settings->add( $setting );
    }

    protected function buildWikiRelatedUI( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('WikiTemplateList');
        
        $setting->setVisibleColumns( array('UID', 'Caption', 'Author', 'UserField1') );
		
        $settings->add( $setting );
    }
}