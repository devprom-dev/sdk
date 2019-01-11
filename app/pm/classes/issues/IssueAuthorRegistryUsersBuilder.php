<?php

include_once "IssueAuthorRegistryBuilder.php";

class IssueAuthorRegistryUsersBuilder extends IssueAuthorRegistryBuilder
{
    function build( IssueAuthorRegistry & $registry )
    {
        if ( !getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant')) ) {
            $registry->merge("
                SELECT t.cms_UserId, 
                       t.Caption, 
                       t.Login, 
                       t.Email, 
                       t.Description, 
                       t.cms_UserId CustomerId, 
                       'User' CustomerClass,
                       t.IsReadonly,
                       t.OrderNum
                  FROM cms_User t
                 WHERE t.cms_UserId = ".getSession()->getUserIt()->getId()."
            ");
            return;
        }

        $predicate = new UserStatePredicate();
        $predicate->setObject(getFactory()->getObject('UserActive'));

        $registry->merge("
                SELECT t.cms_UserId, 
                       t.Caption, 
                       t.Login, 
                       t.Email, 
                       t.Description, 
                       t.cms_UserId CustomerId, 
                       'User' CustomerClass,
                       t.IsReadonly,
                       t.OrderNum
                  FROM cms_User t
                 WHERE 1 = 1 ". $predicate->getPredicate('nonblocked')."
            ");
    }
}