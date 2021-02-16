<?php

include 'MailerForm.php';

class MailerPage extends AdminPage
{
    function getTable()
    {
        return new MailerForm( getFactory()->getObject('MailerSettings')->getAll() );
    }

    function getEntityForm()
    {
        return null;
    }
}

