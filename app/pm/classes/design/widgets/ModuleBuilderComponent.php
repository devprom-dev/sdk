<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleBuilder.php";

class ModuleBuilderComponent extends ModuleBuilder
{
    public function build( ModuleRegistry & $object )
    {
        $modules = array();

        $item = array();
        $item['cms_PluginModuleId'] = 'components-list';
        $item['Caption'] = text(3316);
        $item['AccessEntityReferenceName'] = 'pm_Component';
        $item['Url'] = 'components/list';
        $item['Icon'] = 'icon-stop';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'components-trace';
        $item['Caption'] = text(3315);
        $item['AccessEntityReferenceName'] = 'pm_Component';
        $item['Url'] = 'components/trace';
        $item['Icon'] = 'icon-stop';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'components-chart';
        $item['Caption'] = text(3317);
        $item['AccessEntityReferenceName'] = 'pm_Component';
        $item['Url'] = 'components/chart';
        $item['Icon'] = 'icon-signal';
        $modules[] = $item;

        foreach( $modules as $module ) {
        	$module['Area'] = FUNC_AREA_MANAGEMENT;
            $object->addModule( $module );
        }
    }
}