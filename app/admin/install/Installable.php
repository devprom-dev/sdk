<?php

abstract class Installable
{
	var $errors, $factory;

	function Installable()
	{
		$this->errors = array();
	}

	// checks all required prerequisites
	function check()
	{
		return false;
	}

	// makes install actions
	// returns true|false
	abstract function install();

	// skip install actions
	function skip()
	{
		return false;
	}

	// reports an error
	function raise( $message )
	{
		array_push( $this->errors, $message );
	}

	function result()
	{
		return $this->errors;
	}

	// cleans after installation scripts have been executed
	function cleanup()
	{
		// unlink(__FILE__);
	}
	
	function setFactory( $factory )
	{
		$this->factory = $factory;
	}
	
	function error( $message )
	{
	    if ( !is_object($this->factory) ) return;
	    
		$this->factory->error( $message );
	}

	function info( $message )
	{
	    if ( !is_object($this->factory) ) return;
	    
	    $this->factory->info( $message );
	}

    function checkWindows()
    {
        global $_SERVER;

        return strpos($_SERVER['OS'], 'Windows') !== false
            || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }
    
    function checkPHPVersionNoLessThan( $version )
    {
        return version_compare(phpversion(), $version, '>=') == 1;
    }
}
