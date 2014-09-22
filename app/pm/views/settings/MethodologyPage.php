<?php

include "MethodologyForm.php";

class MethodologyPage extends PMPage
{
    function __construct()
    {
        parent::__construct();

        $this->addInfoSection( new PMLastChangesSection(getSession()->getProjectIt()->getMethodologyIt()) );
    }

    function getTable()
    {
        return null;
    }

    function getForm()
    {
        $form = new MethodologyForm(getFactory()->getObject('pm_Methodology'));

        if ( $form->getAction() == 'show' )
        {
            $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

            if ( getFactory()->getAccessPolicy()->can_modify($methodology_it) )
            {
                $form->edit($methodology_it->getId());
            }
            else
            {
                $form->show($methodology_it->getId());
            }
        }
        	
        return $form;
    }

    function needDisplayForm()
    {
        return true;
    }
}