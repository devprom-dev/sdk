<?php

if ( file_exists(DOCUMENT_ROOT.'conf/plugins/ee/settings.php') ) {
    require_once(DOCUMENT_ROOT.'conf/plugins/ee/settings.php');
}

define('LDAP_PAGESIZE', 300);
 
class LDAP
{
 	var $server, $user, $pass, $connection, $log;
 	
 	function __construct()
 	{
 		global $model_factory;
 		
 		$this->server = LDAP_SERVER;
 		$this->user = LDAP_USERNAME;
 		$this->pass = LDAP_PASSWORD;
 		$this->connection = null;

 		try
 		{
 			$this->log = Logger::getLogger('LDAP');
 		}
		catch( Exception $e )
		{
			error_log('Unable initialize logger: '.$e->getMessage());
		}
 		
 		$settings = $model_factory->getObject('cms_SystemSettings');
 		$this->settings_it = $settings->getAll();
 	}
 	
 	function __destruct()
 	{
 		if ( $this->connection ) {
 			ldap_unbind( $this->connection );
 		}
 	}
 	
 	function setServer( $server )
 	{
 		$this->server = $server;
 	}
 	
 	function getServer()
 	{
 		return $this->server;
 	}

  	function setUserName( $user )
 	{
 		$this->user = $user;
 	}

   	function setPassword( $pass )
 	{
 		$this->pass = $pass;
 	}
 	
 	function warn( $message )
 	{
 		if ( !is_object($this->log) ) return;
 		$this->log->warn( $message );
 	}
 	
 	function info( $message )
 	{
 		if ( !is_object($this->log) ) return;
 		$this->log->info( $message );
 	}
 	
 	function debug( $message )
 	{
 		if ( !is_object($this->log) ) return;
 		$this->log->debug( $message );
 	}

 	function error( $message )
 	{
 		if ( !is_object($this->log) ) return;
 		$this->log->error( $message );
 	}
 	
 	function connect()
 	{
		$parts = preg_split('/:/', $this->server);
		if ( count($parts) < 2 ) {
			$parts[] = 389;
		}

		$this->connection = @ldap_connect( $parts[0], $parts[1] );
		if ( !$this->connection ) 
		{
 			$this->error( str_replace('%2', ldap_error(),
 				str_replace('%1', $this->server, text(2764))) );
			return false;
		}
		
		@ldap_set_option( $this->connection, LDAP_OPT_PROTOCOL_VERSION, 3 );
		@ldap_set_option( $this->connection, LDAP_OPT_REFERRALS, 0 );
		@ldap_set_option( $this->connection, LDAP_OPT_SIZELIMIT, 5000 );
		
		if ( ! @ldap_bind( $this->connection, $this->user, $this->pass ) ) 
		{
 			$this->error( str_replace('%2', ldap_error($this->connection),
 				str_replace('%1', $this->user, text(2765))) );
			return false;
		}
		
 		$this->info( text(2766) );
		return true;
 	}

 	function getNodes( $domain, $attrs = array(), $query = LDAP_ROOTQUERY, $offset = 0 )
 	{
 		$entries = array();
 		
 		if ( count($attrs) < 1 )
 		{
 			$attrs = array(LDAP_ATTR_OU, 'objectClass', 
 				LDAP_ATTR_CN, LDAP_TITLE_ATTR, LDAP_LOGIN_ATTR, LDAP_ATTR_NAME, LDAP_ATTR_MEMBEROF, LDAP_TREEQUERY); 
 		}

        $cookie = '';
 		do {
 			if ( function_exists('ldap_control_paged_result') )
 				@ldap_control_paged_result( $this->connection, LDAP_PAGESIZE, false, $cookie );

			$this->debug('ldap_search, domain: '.$domain);
			$this->debug('ldap_search, query: '.$query);
			$this->debug('ldap_search, attributes: '.var_export($attrs,true));

	 		$result = @ldap_search($this->connection, $domain, $query, $attrs);
			$this->debug('ldap_search, result: '.var_export($result,true));

			if ( !$result ) {
	 			$this->error( str_replace('%3', ldap_error($this->connection),
	 				str_replace('%1', ldap_escape($domain, "", LDAP_ESCAPE_DN),
	 					str_replace('%2', ldap_escape($query, "", LDAP_ESCAPE_FILTER), text(2773)))) );
	 				
	 			return array();
	 		}
 			
 			$entries = array_merge($entries, @ldap_get_entries( $this->connection, $result ));
			$this->debug('ldap_get_entries, entries count: '.count($entries));

	 		$this->info( str_replace('%4', ldap_error($this->connection),
	 			str_replace('%1', $domain, 
	 				str_replace('%2', $query, 
	 					str_replace('%3', $entries['count'], text(2786))))) );

 			if ( function_exists('ldap_control_paged_result_response') ) {
                if (!@ldap_control_paged_result_response($this->connection, $result, $cookie)) {
                    $this->error('ldap_control_paged_result_response, result: ' . ldap_error($this->connection));
                    break;
                }
            }
            $this->debug('ldap_control_paged_result_response, cookie: '.var_export($cookie,true));
 		} 
 		while( $cookie !== null && $cookie != '' );
 		
 		if ( function_exists('ldap_control_paged_result') )
 			@ldap_control_paged_result($this->connection, 0);
 		
 		return $entries;
 	}
 	
 	function getNodeAttributes( $domain, $attrs )
 	{
		$this->debug('ldap_search, domain: '.$domain);
		$this->debug('ldap_search, query: '.LDAP_ROOTQUERY);
		$this->debug('ldap_search, attributes: '.var_export($attrs,true));

 		$result = ldap_search($this->connection, $domain, LDAP_ROOTQUERY, $attrs);
		$this->debug('ldap_search, result: '.var_export($result,true));

 		if ( !$result ) 
 		{
 			$this->warn( str_replace('%1', $domain, 
 				str_replace('%2', LDAP_ROOTQUERY, text(2773)) ) );
 				
 			return array();
 		}
 		
 		$entries = ldap_get_entries( $this->connection, $result );
		$this->debug('ldap_get_entries, entries: '.var_export($entries,true));

 		return $entries[0];
 	}
 	
 	function getAttributeValue( $value, $attr ) {
 		return is_array($value[$attr]) ? $value[$attr][0] : $value[$attr];
 	}

  	function getAttributeArray( $value, $attr )
 	{
 		if ( !is_array($value[$attr]) ) 
 			return array( $this->getAttributeValue( $value, $attr ) );
 		
 		$items = array();
 		
 		foreach( $value[$attr] as $key => $name ) {
 			if ( is_numeric($key) ) {
 				array_push($items, $name);
			}
 		}
 		
 		return $items; 
 	}

 	function getGroupTitle( $value )
	{
		$title = $this->getAttributeValue( $value, LDAP_ATTR_NAME );
		if ( $title == "" ) $title = $this->getAttributeValue( $value, LDAP_ATTR_CN );
		if ( $title == "" ) $title = $this->getAttributeValue( $value, LDAP_ATTR_OU );
		
		if ( $title == "" ) $title = $this->getAttributeValue( $value, LDAP_ATTR_DN );
		return $title;
	}
}
