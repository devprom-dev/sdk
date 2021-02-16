<?php

include_once "AuthenticationLDAPFactory.php";

class AuthenticationLDAPMixedFactory extends AuthenticationLDAPFactory
{
 	function ready()
 	{
		return $_SERVER['PHP_AUTH_USER'] != '' // authenticated user
			&& $_SERVER['PHP_AUTH_MODE'] == 'mixed' // special case with anonymous support
			&& $_SERVER['PHP_AUTH_USER'] != $_SERVER['REMOTE_USER']; // only when LDAP auth provider was used 
 	}
} 