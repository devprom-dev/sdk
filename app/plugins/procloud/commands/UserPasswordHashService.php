<?php

class UserPasswordHashService
{
	public function getHash( $password, $query_string )
	{
		$matches = array();
		
		if ( !preg_match('/CID=([^&]+)/', $query_string, $matches) ) return '';
		
		return md5(strtolower($password).$matches[1]);
	}
	
	public function storePassword( $user_it, $hash )
	{
		if ( $hash == '' ) return;
		
		$user_it->modify(array('SessionHash' => $hash));
	}
	
	public function getInstancePassword( $user_it )
	{
		return $user_it->get('SessionHash');
	}
}