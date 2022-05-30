<?php

class MigrateDatabaseInnoDB extends Installable
{
	function skip()
	{
	    if ( $this->checkWindows() ) return true;

		$version = $this->getMySQLVersion();
		$this->info('MySQL version is ' . $version);
		if ( TextUtils::versionToString($version) < TextUtils::versionToString('5.6') ) return true;

		return false;
	}

    // checks all required prerequisites
    function check() {
    	return true;
    }

    function install()
    {
    	$it = getFactory()->getObject('cms_SystemSettings')->createSQLIterator("show tables");
    	while( !$it->end() )
    	{
    		$table_name = $it->get(0);
    		if ( strpos($table_name, 'v_') !== false ) { // skip custom views
                $it->moveNext();
                continue;
            }
            $result = DAL::Instance()->QueryArray("show create table ".$table_name);
    		if ( strpos(strtolower($result[1]), "engine=innodb") !== false ) {
                $it->moveNext();
                continue;
            }
    		$this->info('Change engine for '.$table_name);

			DAL::Instance()->Query("SET wait_timeout=600");
			DAL::Instance()->Query("SET interactive_timeout=600");
			DAL::Instance()->Query("SET innodb_lock_wait_timeout=600");
			DAL::Instance()->Query("ALTER TABLE ".$table_name." engine=InnoDB");

    		$it->moveNext();
    	}
    	return true;
    }

	protected function getMySQLVersion() {
		return array_shift(DAL::Instance()->QueryArray('SELECT VERSION()'));
	}
}
