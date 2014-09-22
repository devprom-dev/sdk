<?php

class SCMCredentials
{
 	var $url, $path, $login, $pass;
 	
 	function __construct( $url, $path, $login, $pass )
 	{
 		$this->url = $url;
 		$this->path = $path;
 		$this->login = $login;
 		$this->pass = $pass; 
 	}
 	
 	function getUrl()
 	{
 		return $this->url;
 	}
 	
 	function getPath()
 	{
 		return $this->path;
 	}
 	
 	function getLogin()
 	{
 		return $this->login;
 	}
 	
 	function getPassword()
 	{
 		return $this->pass;
 	}
}
