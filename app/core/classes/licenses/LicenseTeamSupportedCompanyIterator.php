<?php

class LicenseTeamSupportedCompanyIterator extends LicenseTeamSupportedIterator
{
	protected function getLimit()
	{
		return 30;
	}
}
