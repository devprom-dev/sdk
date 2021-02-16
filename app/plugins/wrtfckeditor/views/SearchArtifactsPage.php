<?php
include "SearchArtifactsForm.php";

class SearchArtifactsPage extends PMPage
{
    function getObject() {
        return getFactory()->getObject('Request');
    }

    function getEntityForm() {
        return new SearchArtifactsForm( $this->getObject() );
    }

    function needDisplayForm() {
        return true;
    }
}