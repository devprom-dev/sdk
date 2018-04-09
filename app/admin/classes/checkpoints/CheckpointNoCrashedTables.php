<?php

class CheckpointNoCrashedTables extends CheckpointEntryDynamic
{
    function execute()
    {
        $result = DAL::Instance()->QueryArray("show create table pm_Project");
        if ( strpos(strtolower($result[1]), "engine=innodb") !== false ) {
            $this->setValue( '1' );
            return;
        }

        $system = getFactory()->getObject('cms_SystemSettings');
        $system_it = $system->createSQLIterator( "show table status" );

        $tables = array_filter( $system_it->fieldToArray('Name'), function($value) {
        	return $value != '0' && strtolower($value) != 'objectchangelog' && strtolower($value) != 'cms_entitycluster';
        });
        
        if ( count($tables) > 0 )
        {
            $system->createSQLIterator( "check table ".join(",", $tables) );
        }

        $system_it = $system->createSQLIterator( "show table status where Comment like '%crashed%'" );

        $tables = $system_it->fieldToArray('Name');
        if ( count($tables) > 0 && $tables[0] != '0' )
        {
            $this->info(
                var_export(
                    $system->createSQLIterator("repair table ".join(",", $tables)." use_frm ")->getRowset(),
                    true
                )
            );
            $system_it = $system->createSQLIterator( "show table status where Comment like '%crashed%'" );
            $this->setValue( $system_it->count() < 1 ? '1' : '-1' );
        }
        else
        {
            $this->setValue( '1' );
        }
    }

    function getTitle()
    {
        return 'DB: Crashed tables';
    }

    function getDescription()
    {
        return text(1181);
    }
}