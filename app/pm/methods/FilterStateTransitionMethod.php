<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class FilterStateTransitionMethod extends FilterWebMethod
{
    var $state, $state_it;

    function __construct( $object = null )
    {
        parent::__construct();
        $this->object = $object;
        $this->setValueParm('transition');
        $this->setCaption(text(1867));
    }

    function getValues()
    {
        $values = array (
            'all' => translate('Все'),
        );

        $transition_it = WorkflowScheme::Instance()->getTransitionIt($this->object);
        while ( !$transition_it->end() )
        {
            $values[' '.$transition_it->getId()] = $transition_it->getDisplayName().' ('.
                $transition_it->get('SourceStateName').' > '.$transition_it->get('TargetStateName').')';
            $transition_it->moveNext();
        }
        return $values;
    }

    function getStyle() {
        return 'width:120px;';
    }

    function hasAccess() {
        return $this->object instanceof MetaobjectStatable && $this->object->getStatableClassName() != '';
    }
}