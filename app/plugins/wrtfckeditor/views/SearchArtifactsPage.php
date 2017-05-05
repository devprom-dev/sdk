<?php
include "SearchArtifactsForm.php";

class SearchArtifactsPage extends PMPage
{
    function getObject() {
        return getFactory()->getObject('Request');
    }

    function getForm() {
        return new SearchArtifactsForm( $this->getObject() );
    }

    function needDisplayForm() {
        return true;
    }
}