<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingFeatureBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $feature = getFactory()->getObject('Feature');

        $setting = new PageListSetting('FunctionTreeGrid');
        $setting->setVisibleColumns(
            array(
                'UID', 'Caption', 'Progress', 'StartDate', 'DeliveryDate', 'Fact'
            )
        );
        $settings->add( $setting );

        $setting = new PageListSetting('FunctionList');
        $setting->setVisibleColumns(
            array_diff(
                array_merge(
                    array( 'UID', 'Caption', 'Progress' ),
                    $feature->getAttributesByGroup('trace')
                ),
                $feature->getAttributesByGroup('system')
            )
        );
        $settings->add( $setting );
    }
}