<?php
include_once "LicenseTeamSupportedCompanyIterator.php";

class LicenseTeamSupportedCompany extends LicenseTeamSupported
{
    function createIterator()
    {
        return new LicenseTeamSupportedCompanyIterator( $this );
    }
}
