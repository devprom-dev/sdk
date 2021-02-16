<?php

class MigrateTablesTrueUTF8 extends Installable
{
    function skip()
    {
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
        $tables = array(
            'Comment',
            'pm_Task',
            'pm_Integration',
            'pm_ChangeRequest',
            'WikiPage',
            'WikiPageChange',
            'pm_Question',
            'BlogPost',
            'pm_AttributeValue',
            'ObjectChangeLog',
            'EmailQueue'
        );
        foreach( $tables as $table_name )
        {
            $result = DAL::Instance()->QueryArray("show create table ".$table_name);
            if ( stripos($result[0], 'utf8mb4') === false ) {
                DAL::Instance()->Query("ALTER TABLE ".$table_name." CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }
        }

        return true;
    }

    protected function getMySQLVersion() {
        return array_shift(DAL::Instance()->QueryArray('SELECT VERSION()'));
    }
}
