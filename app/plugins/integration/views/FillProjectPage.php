<?php
include "FillProjectForm.php";

class FillProjectPage extends CoPage
{
    function getForm()
    {
        return new FillProjectForm( getFactory()->getObject('Integration') );
    }

    function needDisplayForm()
    {
        return true;
    }
}
