<?php

class ZipSystem
{
    static public function zip( $target, $path, $items = array() )
    {
        if ( EnvironmentSettings::getWindows() ) {
            $command = SERVER_ROOT.'/tools/7za.exe a -r -tzip %1 %2 ';
        }
        else {
            $command = defined('ZIP_APPEND_COMMAND') ? ZIP_APPEND_COMMAND : 'zip -r %1 %2 ';
        }

        $targetZip = dirname($target) . '/' . basename($target) . '.zip';
        $command = preg_replace('/%1/', $targetZip, $command);
        $command = preg_replace('/%2/', join(' ', $items), $command);
        $command = preg_replace('/%3/', '', $command);

        chdir($path);
        @unlink($target);
        $result = shell_exec($command);
        rename($targetZip, $target);
        return $result;
    }

    static public function zipAll( $target, $path )
    {
        $items = array();
        foreach(glob($path . '/*') as $file) {
            $items[] = basename($file);
        }
        return self::zip($target, $path, $items);
    }

    static public function unzip( $source, $path )
    {
        if ( EnvironmentSettings::getWindows() ) {
            $command = SERVER_ROOT.'/tools/7za.exe x -tzip -y %1 ';
        }
        else {
            $command = defined('UNZIP_COMMAND') ? UNZIP_COMMAND : 'unzip %1 ';
        }

        $command = preg_replace('/%1/', $source, $command);

        chdir($path);
        return shell_exec($command);
    }

    static public function sendResponse( $seconds = 0 )
    {
        echo " ";
        ob_flush();
        flush();
        if ( connection_aborted() ) exit();
        if ( $seconds > 0 ) sleep($seconds);
    }
}