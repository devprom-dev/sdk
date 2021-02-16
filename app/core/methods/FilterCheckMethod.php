<?php
include_once "FilterWebMethod.php";

class FilterCheckMethod extends FilterWebMethod
{
 	var $object;
 	var $has_all;
 	var $parmvalue;
 	var $it = null;
 	var $idfieldname;
 	var $title;

 	function __construct( $title = '', $parmvalue = '', $has_all = true )
 	{
 		parent::__construct();

 		$this->title = $title;
 		$this->has_all = $has_all;
 		$this->parmvalue = $parmvalue;
	}
 	
 	function getModule() {
 		return '';
 	}

 	function setIdFieldName( $field ) {
 		$this->idfieldname = $field;
 	}
 	
 	function getStyle() {
 		return 'width:180px;';
 	}

	function getCaption() {
		return $this->title;
	}

	function getValues()
	{
        return array(
            'all' => text(2248),
            'Y' => translate('Да'),
            'N' => translate('Нет')
        );
    }
	
	function getValueParm()
	{
		return $this->parmvalue;
	}

 	function drawSelect( $parms_array = array() ) 
 	{
 		SelectRefreshWebMethod::drawSelect( 
 			array('setting' => $this->method_name,
 				  'object' => $this->getValueParm() ), 
 			$this->getValue() 
 		);
 	}
}