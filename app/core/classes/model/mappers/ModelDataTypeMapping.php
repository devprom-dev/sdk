<?php

abstract class ModelDataTypeMapping
{
	abstract public function applicable( $type_name );
	abstract public function map( $value, array $groups = array() );
	
	public function mapInstance( $attribute, $values, array $groups ) {
		return $this->map($values[$attribute], $groups);
	}
}
