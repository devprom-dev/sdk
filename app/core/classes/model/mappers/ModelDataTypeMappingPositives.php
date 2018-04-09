<?php

class ModelDataTypeMappingPositives
{
	public function mapInstance( &$values )
    {
        if ( $values['OrderNum'] != '' ) {
            $values['OrderNum'] = abs($values['OrderNum']);
        }
	}
}
