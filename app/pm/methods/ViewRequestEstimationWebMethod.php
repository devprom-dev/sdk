<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewRequestEstimationWebMethod extends FilterWebMethod
{
    private $scale = array();

    function __construct( $scale = array() ) {
        $this->scale = $scale;
        parent::__construct();
    }

    function getCaption() {
        return translate('Трудоемкость');
    }

    function getValues()
    {
        $values = array (
            'all' => translate('Все'),
        );
        $values = array_merge($values, $this->scale);
        $values['none'] = translate('Неоцененные');
        return $values;
    }

    function getStyle()
    {
        return 'width:125px;';
    }

    function getValueParm()
    {
        return 'estimation';
    }

    function getType()
    {
        return 'singlevalue';
    }
}
