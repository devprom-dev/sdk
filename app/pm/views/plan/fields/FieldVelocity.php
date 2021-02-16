<?php
include_once SERVER_ROOT_PATH . "pm/classes/model/validators/ModelValidatorVelocity.php";

class FieldVelocity extends FieldNumber
{
    private $strategy = null;

    function __construct( $strategy ) {
        $this->strategy = $strategy;
    }

    function getValue() {
        $value = parent::getValue();
        if ( $value != '' && $this->strategy instanceof EstimationTShirtStrategy ) {
            return $this->strategy->getDimensionText($value);
        }
        else {
            return $value;
        }
    }

    function getValidator() {
        return new ModelValidatorVelocity($this->strategy);
    }
}