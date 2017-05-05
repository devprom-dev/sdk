<?php

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
        foreach(glob($dir . '/*') as $file) {
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
}