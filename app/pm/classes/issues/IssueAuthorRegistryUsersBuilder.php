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
                       t.OrderNum,
                       0
                  FROM cms_User t
                 WHERE t.cms_UserId = ".getSession()->getUserIt()->getId()."
            ");
            return;
        }

        $registry->merge("
                SELECT t.cms_UserId, 
                       t.Caption, 
                       t.Login, 
                       t.Email, 
                       t.Description, 
                       t.cms_UserId CustomerId, 
                       'User' CustomerClass,
                       t.IsReadonly,
                       t.OrderNum,
                       (SELECT COUNT(1) FROM cms_BlackList i WHERE i.SystemUser = t.cms_UserId ) Blocks 
                  FROM cms_User t
                 WHERE 1 = 1
            ");
    }
}