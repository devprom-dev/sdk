<?php
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class LDAPImport extends CommandForm
{
 	function getLogger()
 	{
 		try
 		{
 			return Logger::getLogger('LDAP');
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 			
 			return null;
 		}
 	}
 	
 	function validate()
 	{
 		$settings = getFactory()->getObject('cms_SystemSettings');
 		$this->settings_it = $settings->getAll();
 		
 		return true;
 	}
 	
 	function create()
	{
        $names = $_REQUEST['nodes'];
		if ( count($names) < 1 ) {
			$this->replyError( text(2789) );
		}
		
		$ldap = new LDAP();

		if ( !$ldap->connect() ) {
			$this->replyError( str_replace('%1', $ldap->getServer(), text(2764)) );
		}
		
		$user = getFactory()->getObject('cms_User');
		$user->setNotificationEnabled(false);
		
		$user_it = $user->getAll();
		$user_it->buildPositionHash( array('Login') );
		
		$group = getFactory()->getObject('co_UserGroup');
		$group_it = $group->getAll();
		$group_it->buildPositionHash( array('Caption') );
		
		$attrs = array( 'objectClass', 
			LDAP_ATTR_DN, LDAP_ATTR_OU, LDAP_GROUP_ATTR, 
			LDAP_TITLE_ATTR, LDAP_LOGIN_ATTR, LDAP_EMAIL_ATTR, 
			LDAP_DESCRIPTION_ATTR, LDAP_ATTR_MEMBEROF, LDAP_TREEQUERY );
			
		$user_map = array();
		$group_map = array();
		$member_map = array();
		
		foreach ( $names as $key => $domain )
		{
			$domain = $user_it->utf8towin($domain);
			
			$values = $ldap->getNodeAttributes( $domain, $attrs );
			$dn = $ldap->getAttributeValue( $values, LDAP_ATTR_DN );

			$is_group = false;
			foreach( preg_split('/,/', LDAP_CLASS_OU) as $class_name ) {
				if ( ($is_group = ($is_group || $this->hasClass( $values, $class_name ))) ) break;
			}
				
			if ( $is_group )
			{
				$caption = $ldap->getGroupTitle($values);

				if ( $caption == '' ) {
					$ldap->info( str_replace('%1', $dn, text(2774)) );
					continue;
				}
				
				$group_it->moveTo( 'Caption', $caption );
				if ( $group_it->get('Caption') != $caption )
				{
					$group_id = $group->add_parms(
						array( 'Caption' => $caption, 'LDAPUID' => $dn ) 
					);
				
					$ldap->info( str_replace('%1', $dn, text(2775)) );
				}
				else
				{
					$group_id = $group_it->getId();
				}
				
				$group_map[$dn] = $group_id;
			}
			else
			{
				$login = $ldap->getAttributeValue($values, LDAP_LOGIN_ATTR);
				
				if ( $login == '' ) {
					$ldap->info( str_replace('%2', LDAP_LOGIN_ATTR,
						str_replace('%1', $domain, text(2776))) );
					continue;
				}
				
				$email = $ldap->getAttributeValue($values, LDAP_EMAIL_ATTR);
				
				$caption = $ldap->getAttributeValue($values, LDAP_TITLE_ATTR) != "" ? 
					$ldap->getAttributeValue($values, LDAP_TITLE_ATTR) : $login;
					
				$description = $ldap->getAttributeValue($values, LDAP_DESCRIPTION_ATTR);
									
				$user_it->moveTo('Login', $login );
				
				if ( !$user_it->end() )
				{
					$user->modify_parms($user_it->getId(),
						array( 'Email' => $email,
							   'Caption' => $caption,
							   'Description' => $description,
						       'LDAPUID' => $dn ) 
					);
					
					$user_id = $user_it->getId();

					$ldap->info( str_replace('%1', $user_it->getDisplayName(), 
						str_replace('%2', $domain, text(2785)) ) );
				}
				elseif ( getFactory()->getAccessPolicy()->can_create($user) )
				{
					$user_id = $user->add_parms(
						array( 'Login' => $login, 
							   'Email' => $email, 
							   'Caption' => $caption,
							   'Description' => $description,
							   'IsReadonly' => 'Y',
							   'LDAPUID' => $dn )
					);
				    
					$ldap->info( str_replace('%1', $domain, text(2778)) );
					$ldap->debug(var_export($user->getExact($user_id)->getData(), true));
				}
				else 
				{
				    $ldap->info( text(2810) );
				    
				    $user_id = 0;
				}
				
				$user_map[$dn] = $user_id;
			}
			
			$memberOf = $ldap->getAttributeValue( $values, LDAP_TREEQUERY );

			$arcs = preg_split('/,/', $memberOf);
			unset($arcs[0]);
			$member_map[$dn] = trim(str_replace(chr(13),'',str_replace(chr(10),'',join(',',$arcs))));
		}

		$user_link = getFactory()->getObject('co_UserGroupLink');
		$user_link->addSort( new SortAttributeClause('SystemUser') );
		
		$link_it = $user_link->getAll();
		$link_it->buildPositionHash( array('SystemUser') ); 
		
		$group_it = $group->getAll();
		while( !$group_it->end() )
		{
			if ( $group_it->get('LDAPUID') == '' )
			{
				$group_it->moveNext();
				continue;
			}
			
			foreach( $user_map as $domain => $user_id )
			{
				$is_member = strpos($domain, $group_it->get('LDAPUID')) > 0
					|| strtolower($member_map[$domain]) == strtolower($group_it->getHtmlDecoded('LDAPUID'))
					|| strtolower($member_map[$group_it->get('LDAPUID')]) == strtolower($group_it->getHtmlDecoded('LDAPUID'));

				if( $is_member ) 
				{
					$skip = false;
					
					$link_it->moveTo( 'SystemUser', $user_id );
					
					while( !$link_it->end() && $link_it->get('SystemUser') == $user_id )
					{
						if ( $link_it->get('UserGroup') == $group_it->getId() ) {
							$skip = true;
							break;
						}
						$link_it->moveNext();						
					}
					
					if ( !$skip )
					{
						$user_link->add_parms(
							array( 'UserGroup' => $group_it->getId(),
								   'SystemUser' => $user_id )
						);

						$ldap->info( str_replace('%2', $group_it->getDisplayName(), 
							str_replace('%1', $domain, text(2779))) );
					}
				}
			}
			$group_it->moveNext();
		}

        $generator = new UserPicSpritesGenerator();
        $generator->storeSprites();

		$this->replyRedirect( '/admin/ldap/?mode=info' );
	}
	
	function hasClass( $node, $class_name )
	{
		if ( is_array($node['objectClass']) )
		{
			foreach( $node['objectClass'] as $key => $class )
			{
				if ( strcasecmp($class, $class_name) == 0 ) return true;
			}
		}

		if ( is_array($node['objectclass']) )
		{
			foreach( $node['objectclass'] as $key => $class )
			{
				if ( strcasecmp($class, $class_name) == 0 ) return true;
			}
		}
	}
}
