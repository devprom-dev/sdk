<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorVelocity extends ModelValidatorInstance
{
    private $strategy = null;

    public function __construct( $strategy ) {
        $this->strategy = $strategy;
    }

	public function validate( Metaobject $object, array $parms ) {
        if ( $parms['InitialVelocity'] == '' ) return '';
		return !is_numeric($parms['InitialVelocity']) ? text(2514) . $this->strategy->getDimensionText('') : '';
	}
}