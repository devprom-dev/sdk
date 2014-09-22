<?php

class CheckpointMySQLVariables extends CheckpointEntryDynamic
{
    function execute()
    {
        $system = getFactory()->getObject('cms_SystemSettings');

        $system_it = $system->createSQLIterator( "show variables like '%lower_case_table_names%'" );

        if ( $system_it->get('Value') < 1 )
        {
        	$this->setValue('0');
        	
        	return;
        }
        
        $system_it = $system->createSQLIterator( "show variables like '%ft_min_word_len%'" );

        if ( $system_it->get('Value') < 3 )
        {
        	$this->setValue('0');
        	
        	return;
        }
        
        $system_it = $system->createSQLIterator( "show variables like '%group_concat_max_len%'" );

        if ( $system_it->get('Value') < 4294967295 )
        {
        	$this->setValue('0');
        	
        	return;
        }
        
        $system_it = $system->createSQLIterator( "show variables like '%open_files_limit%'" );

        $limit = $this->checkWindows() ? 2048 : 8192;
        
        if ( $system_it->get('Value') < $limit )
        {
        	$this->setValue('0');
        	
        	return;
        }
        
        $this->setValue( '1' );
    }

    function getTitle()
    {
        return 'DB: MySQL settings';
    }

    function getDescription()
    {
        return text(1430);
    }

    function checkWindows()
    {
        global $_SERVER;

        return strpos($_SERVER['OS'], 'Windows') !== false
            || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }
}
