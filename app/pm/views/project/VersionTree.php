<?php

class VersionTree extends VersionList
{
    use PageTreeTrait;

    function combineCaptionWithDescription() {
        return false;
    }

    function getRenderParms()
    {
        $query = parse_url($this->getTable()->getFiltersUrl(), PHP_URL_QUERY);
        return array_merge(
            parent::getRenderParms(),
            array(
                'jsonUrl' =>
                    getSession()->getApplicationUrl($this->getObject()) . 'treegrid/stage?' . $query
            )
        );
    }
}