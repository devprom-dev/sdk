<?php

class ModelDataTypeMappingPositives
{
	public function map( $object, &$values )
    {
        if ( $values['OrderNum'] != '' ) {
            $values['OrderNum'] = abs($values['OrderNum']);
        }
	}
}
