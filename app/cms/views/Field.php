<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorTypeNull.php";

class Field
{
 	var $name;
	var $value;
	var $readonly;
	var $id;
	var $value_was_set;
	var $tabindex;
	var $text;
	var $default;
	var $edit_mode = true;
	var $required = false;
	
	function Field() 
	{
		global $tabindex;
		$tabindex += 1;
		//$this->value_was_set = false;
	}
	
	function draw() {}
	function drawToolbar() {}
	
	function render( & $view )
	{
        $this->draw( $view );
	}
	
	function drawScripts()
	{
	    
	}
	
	function setRequired( $required = true )
	{
	    $this->required = $required;    
	}
	
	function getRequired()
	{
	    return $this->required;
	}
	
	function setName( $name )
	{
		$this->name = $name;
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function setText( $value )
	{
		$this->text = $value;
	}
	
	function getText()
	{
		if ( $this->text != '' )
		{
			return $this->text;
		}
		else
		{
			return IteratorBase::getHtmlValue( html_entity_decode($this->getValue(), ENT_COMPAT | ENT_HTML401, 'cp1251' ) );
		}
	}
	
	function setDefault( $value )
	{
		$this->default = $value;
	}
	
	function getDefault()
	{
		return $this->default;
	}
	
	function setValue( $value )
	{
		$this->value = $value;
	}
	
	function getValue()
	{
		return $this->value == 'NULL' ? '' : $this->value;
	}
	
	function getEncodedValue()
	{
	    return htmlspecialchars(
	    			html_entity_decode($this->getValue(), ENT_QUOTES | ENT_HTML401, 'cp1251'),
	    					ENT_COMPAT | ENT_HTML401, 'cp1251');
	}    
	
	function setReadOnly( $flag )
	{
		$this->readonly = $flag;
	}

	function readOnly()
	{
		return $this->readonly;
	}
	
	function setEditMode( $flag )
	{
	    $this->edit_mode = $flag;    
	}
	
	function getEditMode()
	{
	    return $this->edit_mode;
	}
	
	function setId( $value )
	{
		$this->id = $value;
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function setTabIndex( $tab )
	{
		$this->tabindex = $tab;
	}
	
	function getTabIndex()
	{
		return $this->tabindex;
	}
	
	function getValidator()
	{
		return new ModelValidatorTypeNull();
	}
}