<?php

// PHPLOCKITOPT NOENCODE
 
define('LDAP_TYPE', 'apacheds');
define('LDAP_METADATA_FILEPATH', dirname(__FILE__).'/settings_ldap_'.LDAP_TYPE.'.php');

include LDAP_METADATA_FILEPATH;
