<?php

include_once "WebMethod.php";

class FilterFreezeWebMethod extends WebMethod
{
 	var $method_name, $filters_name, $filter, $subject;
 	
 	private $filter_values = null;
 	
 	function FilterFreezeWebMethod()
 	{
		$this->subject = 0; // current session user
		parent::WebMethod();
 	}
 	
	function setFilter( $filter )
	{
		$this->filter = $filter;
 		$this->Initialize();
	}
	
	function getFilter()
	{
		return $this->filter;
	}
	
	function setSubject( $subject )
	{
		$this->subject = $subject;
	}
	
	function Initialize()
	{ 	
 		$this->filters_name = md5($this->filter);
	}
	
	function getCaption() 
	{
		return text(1285);
	}
	
	function getJSCall( $element_selector, $redirect = "donothing" )
	{
		$this->redirect = $redirect;
		
		return parent::getJSCall( 
			array( 'filter' => $this->filter,
				   'items' => 'function() { return filterLocation.getParametersString(); }',
				   'values' => 'function() { return $(\''.$element_selector.'\').hasClass(\'checked\') ? filterLocation.getValuesString() : filterLocation.getEmptyValuesString(); }',
				   'subject' => $this->subject )
			);
	}
	
	function getRedirectUrl()
	{
		return $this->redirect;
	}
	
	function getDescription()
	{
		return text(646);
	}
	
	function compareStored( $values )
	{
		$object = getSession()->getUserSettings();
		
		$stored = $object->getSettingsValue($this->filters_name, $this->subject);
		
		if ( $stored == '' )
		{
			$stored = $object->getSettingsValue($this->filters_name, -1); 
		}
		
		if ( $stored == '' ) return false;
		
		$filters = preg_split('/;/', $stored);
		
		$stored_values = array();
		
		foreach( $filters as $filter_item )
		{
		    list( $filter_name, $filter_value ) = preg_split('/=/', $filter_item);
		    
		    $stored_values[$filter_name] = $filter_value;
		}
		
		foreach( $values as $key => $value )
		{
		    if ( $stored_values[$key] != $value ) return false;
		}
		
		return true;
	}
	
	function getQueryString()
	{
		$filters = preg_split('/;/',
			getSession()->getUserSettings()->getSettingsValue($this->filters_name, $this->subject));
		
		if ( count($filters) == 1 )
			$filters = preg_split('/,/',
				getSession()->getUserSettings()->getSettingsValue($this->filters_name, $this->subject));

		return join($filters, '&');
	}
	
	function getValue( $filter )
	{
		if ( is_null($this->filter_values) )
		{
			$this->filter_values = array();

			$settings = getSession()->getUserSettings();
			
			if ( !is_object($settings) ) return '';
			
			$value = $settings->getSettingsValue($this->filters_name);
			
			if ( $value == '' )
			{
				$value = $settings->getSettingsValue($this->filters_name, -1); 
			}
			
			if ( $value == '' ) return '';
			
			$filters = preg_split( '/;/', $value );
		
			if ( count($filters) == 1 ) $filters = preg_split( '/,/', $value );
			
			foreach ( $filters as $value )
			{
				list($f_name, $f_value) = preg_split('/=/', $value);
				
				$this->filter_values[$f_name] = $f_value;
			}
		}

		return $this->filter_values[$filter];
	}
	
 	function execute_request()
 	{
		global $_REQUEST;
 		
 		$this->setFilter( $_REQUEST['filter'] );

		if ( $_REQUEST['items'] == '' )
		{
			getSession()->getUserSettings()->setSettingsValue($this->filters_name, "-", $_REQUEST['subject'] == '' ? null : $_REQUEST['subject']);
		}
		else
		{
	 		$items = preg_split('/,/', $_REQUEST['items']);
	 		
	 		$values = preg_split('/;/', IteratorBase::utf8towin($_REQUEST['values']));
	 		
	 		if ( count($values) == 1 ) $values = preg_split('/,/', $_REQUEST['values']);
	
	 		$combined = array();
	 		
	 		foreach ( $items as $index => $item )
	 		{
	 			$combined[] = $item.'='.$values[$index];
	 		}
	 		
	 		$values = array_filter( $values, function( $value ) {
	 			return $value != '';
	 		});

	 		getSession()->getUserSettings()->setSettingsValue(
	 				$this->filters_name,
	 				count($values) > 0 ? join(";", $combined) : "-", 
	 				$_REQUEST['subject'] == '' ? null : $_REQUEST['subject']
	 		); 
		}
 	}
}
 