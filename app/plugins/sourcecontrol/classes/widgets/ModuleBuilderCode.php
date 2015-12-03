<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleBuilder.php";

class ModuleBuilderCode extends ModuleBuilder
{
    private $session = null;

    public function __construct( $session ) {
        $this->session = $session;
    }
    
    public function build( ModuleRegistry & $object )
    {
        return;

        $item = array();
        $item['cms_PluginModuleId'] = 'sourcecontrol/files';
        $item['Caption'] = text('sourcecontrol3');
        $item['AccessEntityReferenceName'] = 'pm_SubversionRevision';
        $item['Area'] = ModuleCategoryBuilderCode::AREA_UID;
        $item['Url'] = 'module/sourcecontrol/files';
        $object->addModule( $item );

        $modules = array (
            'files' =>
                array(
                    'includes' => array( 'sourcecontrol/views/SubversionFilesPage.php' ),
                    'classname' => 'SubversionFilesPage',
                    'title' => text('sourcecontrol3'),
                    'AccessEntityReferenceName' => 'pm_SubversionRevision',
                    'area' => ModuleCategoryBuilderCode::AREA_UID
                ),
            'revision' =>
                array(
                    'includes' => array( 'sourcecontrol/views/SubversionRevisionPage.php' ),
                    'classname' => 'SubversionRevisionPage',
                    'title' => text('sourcecontrol4'),
                    'AccessEntityReferenceName' => 'pm_SubversionRevision',
                    'area' => ModuleCategoryBuilderCode::AREA_UID
                )
        );

        $modules['connection'] = array(
            'includes' => array( 'sourcecontrol/views/SubversionConnectorPage.php' ),
            'classname' => 'SubversionConnectorPage',
            'title' => text('sourcecontrol28'),
            'description' => text('sourcecontrol40'),
            'AccessEntityReferenceName' => 'pm_Subversion',
            'area' => ModuleCategoryBuilderCode::AREA_UID
        );
    }
}