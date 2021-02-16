<?php

class LicenseTrialIterator extends LicenseIterator
{
    function get( $attr )
    {
        if ( $attr == 'LeftDays' ) {
            return $this->getLeftDays();
        }
        return parent::get( $attr );
    }

    function get_native( $attr )
    {
        if ( $attr == 'LeftDays' ) {
            return $this->getLeftDays();
        }
        return parent::get_native( $attr );
    }

    function valid() {
		if ( !parent::valid() ) return false;
		return $this->getLeftDays() >= 0;
	}

	function getName() {
		return 'Devprom.ALM Trial';
	}
	
	function restrictionMessage( $license_key = '' ) {
	    return text('ee112');
	}

	function getSupportIncluded() {
		return false;
	}

    function checkV1() {
	    return false;
    }
}
