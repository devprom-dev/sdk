<?php

abstract class ModelDataTypeMapping
{
	abstract public function applicable( $type_name );
	abstract public function map( $value );
	
	public function mapInstance( $attribute, $values ) {
		return $this->map($values[$attribute]);
	}
}
