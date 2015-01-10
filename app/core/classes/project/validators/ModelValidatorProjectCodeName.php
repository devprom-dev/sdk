<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorProjectCodeName extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		$parms['CodeName'] = trim($parms['CodeName'], " \r\n");
		
	    if ( strlen($parms['CodeName']) < 3 ) return text(1870);
	    
	    $portfolio_it = getFactory()->getObject('Portfolio')->getAll();

	    while( !$portfolio_it->end() )
	    {
	        if ( $portfolio_it->get('CodeName') == $parms['CodeName'] ) return text(1870);
	        
	        $portfolio_it->moveNext();
	    }
		
		if ( !preg_match ("/^[a-zA-Z0-9][a-zA-Z0-9\-\_]+[a-zA-Z0-9]?$/i", $parms['CodeName']) ) return text(1870);
		
		return "";
	}
}