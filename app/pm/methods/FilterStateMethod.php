<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class FilterStateMethod extends FilterWebMethod
{
    var $state_it, $object;

    function __construct( $object = null, $stateIt = null )
    {
        parent::__construct();
        $this->object = $object;
        $this->state_it = $stateIt;
    }

    function getCaption() {
        return translate('Состояние');
    }

    function getValues()
    {
        $values = array (
            'all' => translate('Любое'),
        );

        $state_it = is_object($this->state_it)
            ? $this->state_it
            : WorkflowScheme::Instance()->getStateIt($this->object);

        while ( !$state_it->end() )
        {
            $values[$state_it->get('ReferenceName')] = $state_it->getDisplayName();
            $state_it->moveNext();
        }

        if ( $state_it->object->getPage() != '?' ) {
            $values = array_merge(
                $values,
                array (
                    '_options' => array( 'uid' => 'options', 'href' => $state_it->object->getPage() )
                )
            );
        }

        return $values;
    }

    function getStyle() {
        return 'width:120px;';
    }

    function getValueParm() {
        return 'state';
    }

    function getValue() {
        $value = parent::getValue();
        if ( $value == '' && $this->getDefaultValue() != '' ) {
            return $this->getDefaultValue();
        }
        return $value;
    }

    function hasAccess() {
        return $this->object instanceof MetaobjectStatable && $this->object->getStatableClassName() != '';
    }
}