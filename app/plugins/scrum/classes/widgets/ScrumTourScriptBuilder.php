<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class ScrumTourScriptBuilder extends ScriptBuilder
{
    private $session = null;

    function __construct( $session ) {
        $this->session = $session;
    }

    public function build( ScriptRegistry & $object )
    {
        if ( defined('SKIP_PRODUCT_TOUR') ) return;

        $project_it = $this->session->getProjectIt();
        if ( !in_array($project_it->get('Tools'), array('scrum_ru.xml','scrumban_ru.xml')) ) return;
        if ( $project_it->getMethodologyIt()->get('UseScrums') != 'Y' ) return;

        $requirements = false;
        $testing = false;
        $code = false;

        foreach ( getFactory()->getPluginsManager()->getPluginsForSection('pm') as $plugin ) {
            if ( $plugin instanceof RequirementsPMPlugin && $plugin->checkEnabled() ) {
                $requirements = $project_it->getMethodologyIt()->get('IsRequirements') != 'N';
            }
            if ( $plugin instanceof TestingPMPlugin && $plugin->checkEnabled() ) {
                $testing = $project_it->getMethodologyIt()->get('IsTests') == 'Y';
            }
            if ( $plugin instanceof SourceControlPMPlugin && $plugin->checkEnabled() ) {
                $code = $project_it->getMethodologyIt()->get('IsSubversionUsed') == 'Y';
            }
        }

        $object->addScriptText(
            preg_replace( '/\%project\%/i', $project_it->get('CodeName'),
                preg_replace('/mode_reqs/i', $requirements ? 'true' : 'false',
                    preg_replace('/mode_qa/i', $testing ? 'true' : 'false',
                        preg_replace('/mode_code/i',  $code ? 'true' : 'false',
                            file_get_contents(
                                $project_it->get('Tools') == 'scrum_ru.xml'
                                    ? SERVER_ROOT_PATH."plugins/scrum/resources/js/tour.js"
                                    : SERVER_ROOT_PATH."plugins/scrum/resources/js/scrumban-tour.js"
                            )
                        )
                    )
                )
            )
        );
    }
}