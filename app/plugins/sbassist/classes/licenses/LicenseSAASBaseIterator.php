<?php

class LicenseSAASBaseIterator extends LicenseIterator
{
	function valid()
	{
	    return $this->object->checkLicense($this);
	}
	
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
	    return text('sbassist19');
	}

    protected function getActiveUsers()
    {
        return getFactory()->getObject('User')->getRegistry()->Count(
            array (
                new UserStatePredicate('active')
            )
        );
    }

    protected function getLimit()
    {
        $users = $this->getUsers();
        return $users > 0 ? $users : $this->getLimitDefault();
    }

    protected function getLimitDefault()
    {
        return 10;
    }

    function allowCreate( & $object )
    {
        if ( !$object instanceof User ) return true;
        return $this->getActiveUsers() < $this->getLimit();
    }}