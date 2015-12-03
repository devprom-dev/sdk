<?php

define ('TEAMUID', '682FEE73-1B33-4266-9192-474F5D59405D');

class LicenseTeamIterator extends LicenseIterator
{
	function valid()
	{
		if ( json_decode(trim($this->get('LicenseValue'))) != null ) return parent::valid();
		return md5(INSTALLATION_UID . TEAMUID) == trim($this->get(LICENSE_WORD . 'Key'));
	}

	function getName()
	{
		return 'Devprom.AgileTeam';
	}

	function getSupportIncluded()
	{
		return false;
	}

	function getLeftDays()
	{
		return max(CheckpointSupportPayed::getPayedDays(), 0);
	}
}
