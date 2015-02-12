<?php

class ServicePayedForm extends PageForm
{
	function createFieldObject( $name ) 
	{
		switch ( $name )
		{
			default:
				return parent::createFieldObject( $name );
		}
	}
}