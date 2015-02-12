<?php

include_once "IssueAuthorRegistryBuilder.php";

class IssueAuthorRegistryUsersBuilder extends IssueAuthorRegistryBuilder
{
    function build( IssueAuthorRegistry & $registry )
    {
    	$registry->merge(getFactory()->getObject('UserActive')->getRegistry()->Query()->getRowset());
    }
}