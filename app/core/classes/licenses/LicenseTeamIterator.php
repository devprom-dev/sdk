<?php
include_once SERVER_ROOT_PATH."admin/classes/checkpoints/CheckpointSupportPayed.php";

define ('TEAMUID', '682FEE73-1B33-4266-9192-474F5D59405D');

class LicenseTeamIterator extends LicenseIterator
{
	function valid()
	{
		if ($this->checkV1()) {
			return md5(INSTALLATION_UID . TEAMUID) == trim($this->get(LICENSE_WORD . 'Key'));
		}
		return parent::valid();
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
