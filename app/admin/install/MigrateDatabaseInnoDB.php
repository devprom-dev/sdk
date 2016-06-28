<?php

class MigrateDatabaseInnoDB extends Installable
{
	function skip()
	{
		$version = $this->getMySQLVersion();
		$this->info('MySQL version is ' . $version);
		if ( TextUtils::versionToString($version) < TextUtils::versionToString('5.6') ) return true;

		$result = DAL::Instance()->QueryArray("show create table pm_Project");
		return strpos(strtolower($result[1]), "engine=innodb") !== false;
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
    		$this->info('Change engine for '.$table_name);
			DAL::Instance()->Query("ALTER TABLE ".$table_name." engine=InnoDB");
    		$it->moveNext();
    	}
    	return true;
    }

	protected function getMySQLVersion() {
		return array_shift(DAL::Instance()->QueryArray('SELECT VERSION()'));
	}
}
