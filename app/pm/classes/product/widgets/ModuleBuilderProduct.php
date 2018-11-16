<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleBuilder.php";

class ModuleBuilderProduct extends ModuleBuilder
{
    public function build( ModuleRegistry & $object )
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( !$methodology_it->HasFeatures() ) return;

        $modules = array();

        $item = array();
        $item['cms_PluginModuleId'] = 'features-list';
        $item['Caption'] = text(2681);
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'features/list';
        $item['Icon'] = 'icon-picture';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'features-trace';
        $item['Caption'] = text(2646);
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'features/trace';
        $item['Icon'] = 'icon-picture';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'features-chart';
        $item['Caption'] = text(2647);
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'features/chart';
        $item['Icon'] = 'icon-signal';
        $modules[] = $item;

        foreach( $modules as $module ) {
        	$module['Area'] = FUNC_AREA_MANAGEMENT;
            $object->addModule( $module );
        }
    }
}