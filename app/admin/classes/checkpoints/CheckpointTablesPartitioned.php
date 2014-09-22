<?php

class CheckpointTablesPartitioned extends CheckpointEntryDynamic
{
    function execute()
    {
        global $model_factory;

        $system = $model_factory->getObject('cms_SystemSettings');
        
        $partition_name = $this->getNewPartitionName();

        foreach( $this->getTables() as $table )
        {
            $table_it = $system->createSQLIterator( "SHOW CREATE TABLE ".$table );
            
            $create_statement = $table_it->get(1);

            if ( strpos($create_statement, 'PARTITION BY') === false ) continue;
            
            if ( strpos($create_statement, $partition_name) === false )
            {
                $this->extendRecentPartition($table, $partition_name);
            }
        }

        $this->setValue( '1' );
    }

    function extendRecentPartition( $table_name, $partition_name )
    {
        $this->info("Extend partition on ".$table_name);
        
        DAL::Instance()->Query(
            "ALTER TABLE ".$table_name." REORGANIZE PARTITION p_max INTO (PARTITION ".$partition_name." VALUES LESS THAN (UNIX_TIMESTAMP('".SystemDateTime::date('Y-m-01')."')),PARTITION p_max VALUES LESS THAN MAXVALUE); "
        );
    }
    
    function getNewPartitionName()
    {
        return strftime('p_%Y%m', strtotime('first day of last month', strtotime(SystemDateTime::date())));
    }
    
    function getTables()
    {
        return array(
                'ObjectChangeLog',
                'cms_EntityCluster'
        );
    }
    
    function getTitle()
    {
        return 'DB: Tables are partitioned';
    }

    function getDescription()
    {
        return text(1427);
    }
}
