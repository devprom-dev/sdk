<?php

 ////////////////////////////////////////////////////////////////////////////
 class InstallLicense extends CommandForm
 {
 	function validate()
 	{
 		$this->checkRequired( array('LicenseKey') );
 		
 		return true;
 	}
 	
 	function create()
	{
		$license = getFactory()->getObject('LicenseInstalled');
		
		$license_it = $license->getAll();
		
		while( !$license_it->end() )
		{
			$license_it->delete();
			
			$license_it->moveNext();
		}
		
		$license->add_parms($_REQUEST);
		
		$this->complete();
	}
	
 	function modify( $object_id )
	{
		$installed = getFactory()->getObject('LicenseInstalled');
		
		$installed->modify_parms( $installed->getAll()->getId(), $_REQUEST );
		
		$this->complete();
	}
	
	function complete()
	{
		getFactory()->resetCache();
		getFactory()->getPluginsManager()->invalidate();

		$it = getFactory()->getObject('LicenseInstalled')->getAll();
		
		if ( $it->get('LicenseType') == '' ) $this->replyError(text(1275));
		if ( !$it->valid() ) $this->replyError($it->restrictionMessage($_REQUEST['LicenseValue']));

        $user = getFactory()->getObject('cms_User');
        if ( $user->getRecordCount() < 1 ) {
            $this->replyRedirect( $user->getPage(), $this->getResultDescription( -1 ) );
        }
        else {
            // report result of the operation
            $this->replyRedirect( '/admin/license/', $this->getResultDescription( -1 ) );
        }
	}

 	function delete()
	{
		$url =  '?LicenseType='.urlencode($_REQUEST['LicenseType']);
		$url .= '&InstallationUID='.INSTALLATION_UID;
		$url .= '&LicenseKey=';
		
		$this->replyRedirect( $url );
	}
	
	function getResultDescription( $result )
	{
		global $model_factory, $_REQUEST;
		
		switch($result)
		{
			case -1:
				return text(234);
				
			case 1:
				return text(200);
		}
	}
 }
