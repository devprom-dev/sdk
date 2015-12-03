<?php
include_once SERVER_ROOT_PATH."admin/classes/checkpoints/CheckpointSupportPayed.php";

class LicenseTeamSupportedIterator extends LicenseIterator
{
	function getName()
	{
		return 'Devprom.AgileTeam';
	}

	function getSupportIncluded()
	{
		return false;
	}

	protected function getActiveUsers()
	{
		return getFactory()->getObject('User')->getRegistry()->Count(
			array (
				new UserStatePredicate('active')
			)
		);
	}

	function getLeftDays()
	{
		return max(CheckpointSupportPayed::getPayedDays(), 0);
	}

	protected function getLimit()
	{
		return 10;
	}

	function allowCreate( & $object )
	{
		if ( !$object instanceof User ) return true;
		return $this->getActiveUsers() < $this->getLimit();
	}
}
