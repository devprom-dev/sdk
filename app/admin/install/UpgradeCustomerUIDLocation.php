<?php

class UpgradeCustomerUIDLocation extends Installable
{
	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// makes install actions
	function install()
	{
	    $content = $this->getSettingsContent();
	    
	    $content = $this->updateConstant( 'CUSTOMER_UID', CUSTOMER_UID, $content );
	    
	    $this->writeSettingsContent( $content );
	     
		return true;
	}
	
    function getSettingsContent()
    {
        return file_get_contents(SERVER_ROOT_PATH.'settings.php');
    }
    
    function writeSettingsContent( $content )
    {
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
