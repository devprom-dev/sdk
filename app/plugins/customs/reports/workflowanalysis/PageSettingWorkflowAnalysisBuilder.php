<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingWorkflowAnalysisBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('ReportWorkflowAnalysisList');
        $setting->setGroup( '_none' );

        $state_columns = array();
        $state_it = WorkflowScheme::Instance()->getStateIt(getFactory()->getObject('Request'));
    	while( !$state_it->end() )
    	{
    		if ( $state_it->get('IsTerminal') == 'Y' ) {
    			$state_it->moveNext();
    			continue;
    		}

    		$state_columns[] = 'State_'.$state_it->getDbSafeReferenceName();
    		$state_it->moveNext();
    	}
        $setting->setVisibleColumns( array_merge(
        		array('UID', 'Caption', 'RecordCreated', 'State', 'Fact', 'LeadTime'), $state_columns
        ));
        $settings->add( $setting );

        $setting = new PageTableSetting('ReportWorkflowAnalysisTable');
        $setting->setSorts( array('State', 'RecordCreated') );
        $settings->add( $setting );
    }
}