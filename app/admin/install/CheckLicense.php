<?php

class CheckLicense extends Installable
{
	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// skip install actions
	function skip()
	{
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup()
	{
	}

	// makes install actions
	function install()
	{
		$license = getFactory()->getObject('LicenseInstalled');
		
		$license_it = $license->getAll();
		
		if ( $license_it->count() < 1 )
		{
			$id = $license->add_parms( array (
				'OrderNum' => 0
			));

			if ( $id < 1 )
			{
				if ( method_exists($this, 'info') ) $this->info( 'Unable to create license object.' );
			}
		}
		else 
		{
			if ( method_exists($this, 'info') ) $this->info( 'License exists already.' );
		}
		
		return true;
	}
}
