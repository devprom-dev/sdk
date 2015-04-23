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
        
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
        $setting = new PageListSetting('TaskList');
        
        $setting->setGroup( 'none' );
        
        $visible_attributes = array('UID', 'Caption', 'State', 'Assignee', 'TaskType', 'ChangeRequest', 'Progress', 'TraceTask');
        
    	if ( $methodology_it->get('IsRequestOrderUsed') == 'Y' )
		{
		    $visible_attributes[] = 'OrderNum';
		}
		else
		{
		    $visible_attributes[] = 'Priority';
		}
		
		$setting->setVisibleColumns( $visible_attributes );
        
        $settings->add( $setting );

        // mytasks report
        
        $setting = new ReportSetting('mytasks');
        
        $setting->setVisibleColumns(
        		array_merge(
        				array (
        						'Spent',
        						'IssueTraces'
        				),
        				array_filter( $visible_attributes, function($value)
        				{
        						return !in_array($value, array('Assignee', 'Progress')); 
        				})
        		)
        );
        
        $settings->add( $setting );

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
   				array_filter( $visible_attributes, function($value)
   				{
   						return !in_array($value, array('ChangeRequest')); 
   				})
        );
        
        $settings->add( $setting );
        
        // tasks-trace report
        
        $setting = new ReportSetting('tasks-trace');
        
 	    $visible = array_merge( 
 	    		array(
 	    				'UID', 
 	    				'Caption',
 	    				'ChangeRequest'
 	    		), 
		    	getFactory()->getObject('Task')->getAttributesByGroup('trace')
		);
        
        $setting->setVisibleColumns($visible);
        
        $settings->add( $setting );
        
        // tasks table
        
        $setting = new PageListSetting('TaskBoardList');

        $columns = array('UID', 'Caption', 'RecentComment', 'Fact', 'Attachment', 'Assignee', 'Progress');

        if ( $methodology_it->get('IsRequestOrderUsed') == 'Y' )
		{
		    $columns[] = 'OrderNum';
		}
        
        $setting->setVisibleColumns($columns);
        
        $settings->add( $setting );

        
        
        
        $setting = new PageTableSetting('TaskTable');
        
        $setting->setFilters( array('release', 'iteration', 'taskstate', 'tasktype', 'taskassignee', 'target') );

       	if ( $methodology_it->get('IsRequestOrderUsed') == 'Y' )
		{
		    $setting->setSorts( array('OrderNum') );
		}
		else
		{
		    $setting->setSorts( array('Priority') );
		}
        
        $settings->add( $setting );
    }

    protected function buildWikiRelatedUI( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('WikiTemplateList');
        
        $setting->setVisibleColumns( array('UID', 'Caption', 'Author', 'UserField1') );
		
        $settings->add( $setting );
    }
}