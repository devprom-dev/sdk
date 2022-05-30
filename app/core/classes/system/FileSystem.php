<?php
use Symfony\Component\Process\Process;

class FileSystem
{
	static public function rmdirr($dir)
	{
		if (!is_dir($dir)) return;
        $newDir = dirname($dir) . '/' . basename($dir) . '-' . md5($dir.uniqid($dir));
		rename($dir, $newDir);
        self::_rmdir($newDir);
	}

	static protected function _rmdir($dir) {
        foreach(glob($dir . '/{,.}*[!.]*', GLOB_BRACE) as $file) {
            if(is_dir($file))
                self::_rmdir($file);
            else
                @unlink($file);
        }
        @rmdir($dir);
    }

	static public function translateError( $error ) {
        switch( $error )
        {
            case '1': // UPLOAD_ERR_INI_SIZE
                return text(1258);
            case '2': // UPLOAD_ERR_FORM_SIZE
                return text(1259);
            case '3': // UPLOAD_ERR_PARTIAL
                return text(1260);
            case '6': // UPLOAD_ERR_NO_TMP_DIR
                return text(1261);
            case '7': // UPLOAD_ERR_CANT_WRITE
                return text(1262);
            case '8': // UPLOAD_ERR_EXTENSION
                return text(1263);
            default:
                return text(2216);
        }
    }

    static function copyPath( $source_path, $destination_path )
    {
        if ($dh = opendir($source_path)) {
            while (($file = readdir($dh)) !== false ) {
                if( $file != "." && $file != ".." ) {
                    if( is_dir( $source_path . $file ) ) {
                        @mkdir($destination_path . $file, 0777, true);
                        self::copyPath( $source_path . $file . "/", $destination_path . $file . "/" );
                    }
                    else {
                        @mkdir($destination_path, 0777, true);
                        $result = copy($source_path.$file, $destination_path.$file);
                    }
                }
            }
            closedir($dh);
        }
    }

    static function execCommand($cmd, $input = '')
    {
        if ( \EnvironmentSettings::getWindows() ) {
            $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        }
        else {
            $process = proc_open($cmd,
                [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null,
                array(
                    'XDG_RUNTIME_DIR' => sys_get_temp_dir()
                )
            );
        }

        if (false === $process) {
            return array();
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($process);

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn,
        ];
    }

    static function execPanDoc( $commands = '-v' )
    {
        $streams = self::execCommand("pandoc " . $commands);
        if ( $streams['stderr'] != '' ) {
            throw new Exception('LibreOffice exception: ' . $streams['stderr']);
        }
        return array_shift(explode(PHP_EOL, $streams['stdout']));
    }

    static function execLibreOffice( $commands = '--version' )
    {
        if ( \EnvironmentSettings::getWindows() ) {
            $streams = self::execCommand("soffice " . $commands);
        }
        else {
            $streams = self::execCommand(
                "soffice -env:UserInstallation='file://".sys_get_temp_dir()."' " . $commands
                );
        }
        if ( $streams['stderr'] != '' ) {
            throw new Exception('LibreOffice exception: ' . $streams['stderr']);
        }
        return array_shift(explode(PHP_EOL, $streams['stdout']));
    }

    static function execAndSendResponse($command)
    {
        $process = new Process($command);

        $process->start();
        while ( $process->isRunning() ) {
            \ZipSystem::sendResponse(1);
        }

        $error = $process->getErrorOutput();
        $error = trim(preg_replace('/.+\[Warning\].+/mi', '', $error));
        if ( $error != '' ) throw new Exception($error);

        return $process->getOutput();
    }
}