<?php

class LicenseEnterpriseIterator extends LicenseIterator
{
	function valid()
	{
		if ( $this->checkV1() ) {
            $user = getFactory()->getObject('EEUser');
            if ( !is_object($user) ) {
                getFactory()->error('EEUser class wasn\'t found');
                return false;
            }
            return $user->checkLicense($this) > 0;
		} else {
            if ( !parent::valid() ) return false;
            return $this->getLeftDays() >= 0;
		}
	}
	
	function getName()
	{
		return 'Devprom.ALM';
	}
	
	function getSupportIncluded()
	{
		return false;
	}

	function getLeftDays() {
	    $leftDays = parent::getLeftDays();
		return $leftDays == '' ? 999 : $leftDays;
	}
}