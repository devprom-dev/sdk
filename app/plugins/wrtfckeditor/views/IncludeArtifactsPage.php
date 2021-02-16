<?php
include "IncludeArtifactsForm.php";

class IncludeArtifactsPage extends PMPage
{
    function getObject() {
        return getFactory()->getObject('Request');
    }

    function getEntityForm() {
        return new IncludeArtifactsForm( $this->getObject() );
    }

    function needDisplayForm() {
        return true;
    }
}