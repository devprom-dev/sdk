<?php

class MigrateDatabaseUTF8 extends Installable 
{
    function skip()
    {
    	if ( APP_ENCODING != 'utf-8' ) return true;
    	
    	$result = mysql_fetch_array(
    			DAL::Instance()->Query("SELECT DEFAULT_CHARACTER_SET_NAME FROM information_schema.SCHEMATA where SCHEMA_NAME = '".DB_NAME."'")
    	);
    	
    	return strtolower(trim($result[0])) == 'utf8';
    }

    // checks all required prerequisites
    function check()
    {
    	return true;
    }

    function install()
    {
    	DAL::Instance()->Query("ALTER TABLE cms_Snapshot MODIFY ObjectId VARCHAR(64)");
    	DAL::Instance()->Query("ALTER TABLE cms_Snapshot MODIFY ObjectClass VARCHAR(64)");
    	DAL::Instance()->Query("ALTER TABLE cms_Snapshot MODIFY Type VARCHAR(64)");
    	
    	$it = getFactory()->getObject('cms_SystemSettings')->createSQLIterator("show tables");
    	while( !$it->end() )
    	{
    		$table_name = $it->get(0);
    		if ( in_array(strtolower($table_name),array('businessfunction')) ) {
    			$it->moveNext();
    			continue;
    		}
    		
    		$this->info('Migration of '.$table_name);
    		
    		$result = mysql_fetch_array(DAL::Instance()->Query("show create table ".$table_name));
    		if ( preg_match('/\([\r\n\s]+`([^`]+)Id`/i', $result[1], $matches) && strtolower($matches[1]) == strtolower($table_name) ) {
    			$table_name = $matches[1];
	    		$this->info('Name adjusted '.$table_name);

    			DAL::Instance()->Query("RENAME TABLE ".$it->get(0)." to ".$table_name."1");
    			DAL::Instance()->Query("RENAME TABLE ".$table_name."1 to ".$table_name);
    		}
    		
    		DAL::Instance()->Query("ALTER TABLE ".$table_name." CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
    		$it->moveNext();
    	}
    	
    	DAL::Instance()->Query("ALTER DATABASE ".DB_NAME." CHARACTER SET utf8 COLLATE utf8_general_ci");
    	
    	// change charset for dumps
    	$settings_path = SERVER_ROOT_PATH.'settings_server.php';
    	$settings = file_get_contents($settings_path);
    	if ( $settings != '' ) {
    		$bytes = file_put_contents($settings_path, preg_replace('/cp1251/i', 'utf8', $settings));
    		$this->info('Settings file fixed "'.SERVER_ROOT_PATH.$settings_path.'": '.$bytes);
    	}
    	
    	return true;
    }
}
