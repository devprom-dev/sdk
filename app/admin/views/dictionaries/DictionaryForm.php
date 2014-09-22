<?php

include SERVER_ROOT_PATH."admin/views/ui/DateFormatDictionary.php";

class DictionaryForm extends PageForm
{
	function createFieldObject( $attr )
	{
		switch ( $attr )
		{
			case 'DateFormatClass':
				return new DateFormatDictionary();
				
			default:
				return parent::createFieldObject( $attr );
		}
	}
}
