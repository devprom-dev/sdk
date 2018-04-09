<?php

include_once "IssueAuthorRegistryBuilder.php";

class IssueAuthorRegistryUsersBuilder extends IssueAuthorRegistryBuilder
{
    function build( IssueAuthorRegistry & $registry )
    {
        if ( !getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant')) ) {
            $rowset = getSession()->getUserIt()->getRowset();
            foreach( $rowset as $row => $data ) {
                $rowset[$row]['CustomerClass'] = 'User';
            }
            $registry->merge($rowset);
            return;
        }

        $rowset = getFactory()->getObject('UserActive')->getRegistry()->Query()->getRowset();
        foreach( $rowset as $row => $data ) {
            $rowset[$row]['CustomerClass'] = 'User';
        }
    	$registry->merge($rowset);
    }
}