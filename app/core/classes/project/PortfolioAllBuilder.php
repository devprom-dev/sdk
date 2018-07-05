<?php
include_once SERVER_ROOT_PATH . "core/classes/project/PortfolioBuilder.php";
include_once SERVER_ROOT_PATH . "core/classes/SessionPortfolioAllProjects.php";

class PortfolioAllBuilder extends PortfolioBuilder
{
    public function build( PortfolioRegistry & $object )
    {
        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $module_it = getFactory()->getObject('Module')->createCachedIterator(
                array (
                    array( 'cms_PluginModuleId' => 'ee/allprojects' )
                )
            );
            if ( !getFactory()->getAccessPolicy()->can_read($module_it) ) return;
        }

		$project_ids = getFactory()->getObject('pm_Project')->getRegistry()->Query(
                            array(
                                new ProjectStatePredicate('active')
                            )
            			)->idsToArray();
        if ( count($project_ids) < 1 ) return;

        $object->addPortfolio( 
            array (
                'pm_ProjectId' => 20000000,
                'BaseId' => 20000000,
                'Caption' => text('projects.all'),
                'CodeName' => 'all',
                'LinkedProject' => join(',',$project_ids),
            	'RelatedProject' => join(',',$project_ids),
                'WikiEditorClass' => 'WikiRtfCKEditor'
            ),
            function( $portfolio_it ) {
                return new SessionPortfolioAllProjects($portfolio_it);
            }
        );
    }
}