<?php
include 'EELDAPConnectForm.php';
include 'EELDAPSelectRulesForm.php';
include 'EELDAPInfoForm.php';
include 'EELDAPMetadataForm.php';

class EELDAPPage extends AdminPage
{
	function getEntityForm()
 	{
 		$settings = getFactory()->getObject('cms_SystemSettings');
 		
 		switch ( $_REQUEST['mode'] )
 		{
 			case 'selectrules':

 				$ldap = new LDAP();
				if ( !$ldap->connect() ) return;
				
				$nodes = $ldap->getNodes( LDAP_DOMAIN, array(), LDAP_ROOTQUERY );
				if ( count($nodes) < 1 ) {
	 				return new EELDAPInfoForm( $settings );
				}
				else {
	 				return new EELDAPSelectRulesForm( $settings );
				}
 			
 			case 'metadata':
 				return new EELDAPMetadataForm( $settings );
	 				
 			case 'info':
 				return new EELDAPInfoForm( $settings );
 				
 			default: 			
 				return new EELDAPConnectForm( $settings );
 		}
 	}
 	
 	function needDisplayForm()
 	{
 		return true;
 	}

 	function export()
 	{
		$ldap = new LDAP();
		
		if ( !$ldap->connect() ) return;
		
		$known_users = getFactory()->getObject('cms_User')->getAll()->fieldToArray('LDAPUID');
		$known_groups = getFactory()->getObject('co_UserGroup')->getAll()->fieldToArray('LDAPUID');
		
		$nodes = $ldap->getNodes( $_REQUEST['lazyroot'], array(), LDAP_ROOTQUERY );
		if ( !array_key_exists('count', $nodes) ) {
			$nodes = array( 1 => $nodes );
		}
		
		$items = array();
		
		foreach( $nodes as $key => $node )
		{
			$ldap->info(var_export($node, true));

			if ( !is_numeric($key) ) {
				$ldap->info("Key is not numeric: ".$key);
				continue;
			}

			if ( count($node) < 1 ) {
				$ldap->info("There are no attributes in the node");
				continue;
			}

    		$node[LDAP_ATTR_DN] = $ldap->getAttributeValue( $node, LDAP_ATTR_DN );
            if ( $node[LDAP_ATTR_DN] == $_REQUEST['lazyroot'] ) continue;

			$node['class'] = $ldap->getAttributeArray( $node, 'objectclass' );
			if ( LDAP_ATTR_MEMBEROF != '' )
			{
				$ldap->info("memberOf attribute has been defined: ".LDAP_ATTR_MEMBEROF);
				
				$arcs = array();
				$native = trim(str_replace( strtolower($domain), '', strtolower($node[LDAP_ATTR_DN]) ), ',');
				if ( $native != '' ) $arcs = preg_split('/,/', $native);

				$node[LDAP_ATTR_MEMBEROF] = $ldap->getAttributeArray( $node, LDAP_ATTR_MEMBEROF );
				$continue = count($arcs) != 1 && !in_array($domain, $node[LDAP_ATTR_MEMBEROF]);
				if ( $continue ) continue;
			}
			
			if ( LDAP_TREEQUERY != '' )
			{
				$ldap->info("treeQuery attribute has been defined: ".LDAP_TREEQUERY);
				
				$node[LDAP_TREEQUERY] = $ldap->getAttributeValue( $node, LDAP_TREEQUERY );

				$arcs = array();
				$native = trim(str_replace(strtolower($domain), '', strtolower($node[LDAP_TREEQUERY])), ',');
				if ( $native != '' ) $arcs = preg_split('/,/', $native);
				if ( count($arcs) != 1 ) continue;
			}

			$checked = in_array($node[LDAP_ATTR_DN], $known_users);
			if ( !$checked ) {
				$checked = in_array($node[LDAP_ATTR_DN], $known_groups);
			}
			
			$key = str_replace('"', '\'', $node[LDAP_ATTR_DN]);
			$title = $ldap->getGroupTitle( $node );
			
			$intersect = array_intersect( $node['class'], preg_split('/,/', LDAP_CLASS_OU) );

            $items[] = array (
                'title' => $checked ? '<b>'.$title.'</b>' : $title,
                'folder' => count($intersect) > 0,
                'key' => $key,
                'expanded' => false,
                'lazy' => count($intersect) > 0
            );
		}
		usort( $items, "ldap_dn_sort" );
		echo JsonWrapper::encode($items);
 	}
}

function ldap_dn_sort( $left, $right )
{
 	return $left['title'] > $right['title'] ? 1 : -1;
}
