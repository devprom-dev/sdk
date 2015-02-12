<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingFeatureBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
        $setting = new PageListSetting('FunctionList');
        $setting->setVisibleColumns( 
        		array(
        			'UID', 'Caption', 'Progress', 'Workload', 'StartDate', 'DeliveryDate', 'Requirement', 'Request'
        		)
        );
        $feature_levels = getFactory()->getObject('FeatureType')->getRegistry()->Count(
        	array ( new FilterVpdPredicate() )
        );
        $setting->setGroup( $feature_levels > 0 ? 'none' : 'Importance' );
        $settings->add( $setting );

        $setting = new PageTableSetting('FunctionTable');
		$setting->setFilters(
				array (
						'state', 'tag', 'type', 'importance'
				)
        );
        $settings->add( $setting );

        $setting = new ReportSetting('features-chart');
        $setting->setVisibleColumns( 
        		array(
        			'UID', 'Caption', 'Workload', 'StartDate', 'DeliveryDate'
        		)
        );
        $settings->add( $setting );
    }
}