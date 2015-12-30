<?php

class SettingsFile
{
    static public function setSettingValue( $parm, $value, $file_content )
    {
        $regexp = "/(define\(\'".$parm."\'\,\s*\'[^']*\'\);)/mi";

        if ( preg_match( $regexp, $file_content, $match ) > 0 )
        {
            $file_content = preg_replace( $regexp,
                "define('".$parm."', '".$value."');", $file_content);
        }
        else
        {
            if ( strpos($file_content, "?>") !== false )
            {
                $file_content = preg_replace( "/(\?>)/mi",
                    "\n\tdefine('".$parm."', '".$value."');\n?>", $file_content);
            }
            else
            {
                $file_content .= "\n\tdefine('".$parm."', '".$value."');\n";
            }
        }
        return $file_content;
    }
}