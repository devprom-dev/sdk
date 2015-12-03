<?php
include_once "LicenseTeamSupportedUnlimitedIterator.php";

class LicenseTeamSupportedUnlimited extends LicenseTeamSupportedCompany
{
    function createIterator()
    {
        return new LicenseTeamSupportedUnlimitedIterator( $this );
    }
}
