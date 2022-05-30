<?php

class ChooseLicenseType extends CommandForm
{
	function validate()
	{
		$this->checkRequired( array('LicenseType') );

		$license_it = getFactory()->getObject('License')->getRegistry()->Query(array());
		
		if ( in_array($_REQUEST['LicenseType'], $license_it->fieldToArray('LicenseType')) ) return true;
		
		$this->replyError(text(1275));
		
		return false;
	}

	function create()
	{
        $url =  '?LicenseType='.urlencode($_REQUEST['LicenseType']);

        $url .= '&InstallationUID='.INSTALLATION_UID;

        $url .= '&LicenseKey=';

        $this->replyRedirect( $url );
	}

    function delete( $object_id )
    {
        $this->create();
    }
}
