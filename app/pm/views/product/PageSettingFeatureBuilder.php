<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingFeatureBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
        $setting = new PageListSetting('FunctionList');
        
        $visible_attributes = array('UID', 'Caption', 'Progress', 'Workload', 'Estimation', 'StartDate', 'DeliveryDate');
        
		$setting->setVisibleColumns( $visible_attributes );
        
        $settings->add( $setting );
    }
}