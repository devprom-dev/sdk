<?php

class CacheEngine
{
	private $default_path = '';
	
	private $readonly = false;
			
	public function __construct( $path = 'global' )
	{
		$this->setDefaultPath($path);
	}
	
	public function setDefaultPath( $path )
	{
		$this->default_path = $path;
	}
	
	public function getDefaultPath()
	{
		return $this->default_path;
	}
	
	public function setReadonly( $flag = true )
	{
		$this->readonly = $flag;
	}
	
	public function getReadonly()
	{
		return $this->readonly;
	}
	
	function get( $key, $path = '' )
	{
	}
	
	function set( $key, $value, $path = '' )
	{
	}
	
	function reset( $key, $path = '' )
	{
		$this->set($key, '', $path);
	}
	
	function truncate( $path )
	{
	}
	
	function drop()
	{
	}
}
