<?php

class LicenseTeamSupportedUnlimitedIterator extends LicenseTeamSupportedIterator
{
	function allowCreate( & $object )
	{
		return true;
	}
}
