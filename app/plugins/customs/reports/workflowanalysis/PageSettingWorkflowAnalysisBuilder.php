<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingWorkflowAnalysisBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new ReportSetting('workflowanalysis');
        
        $setting->setSorts( array('State', 'RecordCreated') );

        $setting->setGroup( '_none' );
        
        $setting->setFilters( array('submittedon', 'submittedbefore', 'state', 'timescale', 'target') );
        
        $state_columns = array();
        
        $state_it = getFactory()->getObject('Request')->cacheStates();
    	
    	while( !$state_it->end() )
    	{
    		if ( $state_it->get('IsTerminal') == 'Y' )
    		{
    			$state_it->moveNext();
    			continue;
    		}

    		$state_columns[] = 'State_'.$state_it->getDbSafeReferenceName();
    		
    		$state_it->moveNext();
    	}
        
        $setting->setVisibleColumns( array_merge(
        		array('UID', 'Caption', 'RecordCreated', 'State', 'Fact'), $state_columns        		 
        ));
        
        $setting->setSections( array('none') );
        
        $settings->add( $setting );
    }
}