<?php
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

class ViewStartDateWebMethod extends FilterDateWebMethod
{
 	function __construct ( $title = 'Начало' ) {
		parent::__construct();
        $this->setCaption(translate($title));
 	}

	function getStyle()	{
		return 'width:100px;';
	}

	function getValueParm()	{
		return 'start';
	}
}
