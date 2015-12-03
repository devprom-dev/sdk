<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class KanbanTourScriptBuilder extends ScriptBuilder
{
    private $session = null;

    function __construct( $session ) {
        $this->session = $session;
    }

    public function build( ScriptRegistry & $object )
    {
        if ( defined('SKIP_PRODUCT_TOUR') ) return;

        $project_it = $this->session->getProjectIt();
        if ( $project_it->get('Tools') != 'kanban_ru.xml' ) return;

        $object->addScriptText(
            preg_replace( '/\%project\%/i', $project_it->get('CodeName'),
                preg_replace('/mode_reqs/i', $project_it->getMethodologyIt()->get('IsRequirements') == 'Y' ? 'true' : 'false',
                    preg_replace('/mode_qa/i', $project_it->getMethodologyIt()->get('IsTests') == 'Y' ? 'true' : 'false',
                        preg_replace('/mode_code/i', $project_it->getMethodologyIt()->get('IsSubversionUsed') == 'Y' ? 'true' : 'false',
                            file_get_contents(SERVER_ROOT_PATH."plugins/kanban/resources/js/tour.js")
                        )
                    )
                )
            )
        );
    }
}