<?php

// PHPLOCKITOPT NOENCODE
 
define('LDAP_SERVER', 'localhost:10389');
define('LDAP_USERNAME', 'uid=admin,ou=system'); 
define('LDAP_PASSWORD', 'secret'); 
define('LDAP_DOMAIN', 'ou=system'); 
define('LDAP_ROOTQUERY', '(objectClass=*)'); 
define('LDAP_ATTRIBUTES', '' ); 
define('LDAP_GROUP_ATTR', 'cn' ); 
define('LDAP_TITLE_ATTR', 'cn' ); 
define('LDAP_NAME_ATTR', 'name' ); 
define('LDAP_LOGIN_ATTR', 'cn'); 
define('LDAP_EMAIL_ATTR', 'mail'); 
define('LDAP_DESCRIPTION_ATTR', 'displayname' ); 
define('LDAP_ATTR_OU', 'ou' ); 
define('LDAP_ATTR_DN', 'dn' ); 
define('LDAP_ATTR_CN', 'cn' ); 
define('LDAP_ATTR_MEMBEROF', '' ); 
define('LDAP_TREEQUERY', ''); 
define('LDAP_CLASS_OP', 'organizationalPerson,person' ); 
define('LDAP_CLASS_OU', 'organizationalUnit,groupOfUniqueNames,group' ); 

?>