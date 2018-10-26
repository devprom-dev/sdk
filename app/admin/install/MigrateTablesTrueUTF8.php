<?php

class MigrateTablesTrueUTF8 extends Installable
{
    function skip()
    {
        if ( APP_CHARSET != 'utf8' ) return true;

        $version = $this->getMySQLVersion();
        $this->info('MySQL version is ' . $version);
        if ( TextUtils::versionToString($version) < TextUtils::versionToString('5.6') ) return true;

        $result = DAL::Instance()->QueryArray("SELECT DEFAULT_CHARACTER_SET_NAME FROM information_schema.SCHEMATA where SCHEMA_NAME = '".DB_NAME."'");
        return strtolower(trim($result[0])) != 'utf8';
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
            'pm_ChangeRequest',
            'WikiPage',
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

        $this->writeSettingsContent(
            $this->updateConstant( 'APP_CHARSET', 'utf8mb4', $this->getSettingsContent() )
        );

        return true;
    }

    protected function getMySQLVersion() {
        return array_shift(DAL::Instance()->QueryArray('SELECT VERSION()'));
    }

    function getSettingsContent() {
        return file_get_contents(SERVER_ROOT_PATH.'settings.php');
    }

    function writeSettingsContent( $content ) {
        file_put_contents(SERVER_ROOT_PATH.'settings.php', $content);
    }

    function updateConstant( $parm, $value, $file_content )
    {
        $regexp = "/(define\(\'".$parm."\'\,\s*\'[^']*\'\);)/mi";

        if ( preg_match( $regexp, $file_content, $match ) > 0 ) return $file_content;

        if ( strpos($file_content, "?>") !== false )
        {
            $file_content = preg_replace( "/(\?>)/mi",
                "\n\tdefine('".$parm."', '".$value."');\n?>", $file_content);
        }
        else
        {
            $file_content .= "\n\tdefine('".$parm."', '".$value."');\n";
        }

        return $file_content;
    }
}
