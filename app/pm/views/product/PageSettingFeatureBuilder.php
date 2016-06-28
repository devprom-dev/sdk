<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingFeatureBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $feature = getFactory()->getObject('Feature');
        
        $setting = new PageListSetting('FunctionList');
        $setting->setVisibleColumns(
            array_diff(
                array_merge(
                    array( 'UID', 'Caption', 'Progress', 'Workload', 'StartDate', 'DeliveryDate', 'Request' ),
                    array_filter($feature->getAttributesByGroup('trace'), function($value) use ($feature) {
                        return true;
                    })
                ),
                $feature->getAttributesByGroup('system')
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
						'state', 'tag', 'type', 'importance', 'parent'
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