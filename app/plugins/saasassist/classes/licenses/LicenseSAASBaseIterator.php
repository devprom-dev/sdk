<?php

class LicenseSAASBaseIterator extends LicenseIterator
{
	function get( $attr )
	{
		if ( $attr == 'LeftDays' ) return $this->object->getLeftDays($this);

		return parent::get( $attr );
	}

	function get_native( $attr )
	{
		if ( $attr == 'LeftDays' ) return $this->object->getLeftDays($this);

		return parent::get_native( $attr );
	}

	function restrictionMessage( $license_key = '' )
	{
		return text('saasassist19');
	}
}