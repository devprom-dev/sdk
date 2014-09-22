<?php

class CheckpointTablesOptimized extends CheckpointEntryDynamic
{
    function execute()
    {
        global $model_factory;

        $system = $model_factory->getObject('cms_SystemSettings');
        $system_it = $system->createSQLIterator( "show table status where Data_free != 0" );

        $tables = $system_it->fieldToArray('Name');
        if ( count($tables) > 0 && $tables[0] != '0' )
        {
            $system->createSQLIterator( "optimize table ".join(",", $tables) );
            $system->createSQLIterator( "analyze table ".join(",", $tables) );
        }

        $this->setValue( '1' );
    }

    function getTitle()
    {
        return 'DB: Optimized tables';
    }

    function getDescription()
    {
        return text(1182);
    }
}
