<?php

class IntegrationReviewBoardChannel extends IntegrationRestAPIChannel
{
    const apiPath = "/api";

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $parms = array();
        if ( $timestamp != '' ) {
            $datetime = new DateTime($timestamp, new DateTimeZone("UTC"));
            $parms['last-updated-from'] = $datetime->format('c');
        }
        $parms['max-results'] = $limit;
        $parms['status'] = 'all';

        // get recent items
        $result = $this->jsonGet(self::apiPath.'/review-requests/', $parms);

        // build working queue
        $latest = array();
        foreach( $result['review_requests'] as $item )
        {
            $latest[$item[$this->getKeyField()]] = array (
                'class' => 'ReviewRequest',
                'id' => $item[$this->getKeyField()]
            );
        }
        return array(
            $latest,
            ''
        );
    }

    protected function buildPostFields( $post ) {
        // multipart/form-data
        return array_shift($post);
    }

    protected function getUserEmailAttribute() {
        return 'email';
    }

    function getKeyValue($data) {
        unset($data['stat']);
        return $data[array_shift(array_keys($data))][$this->getKeyField()];
    }

    protected function buildUsersMap()
    {
        $map = array();
        $result = $this->jsonGet( self::apiPath.'/users/', array(), false );
        foreach( $result['users'] as $user ) {
            $map[$user['name']] = $user[$this->getUserEmailAttribute()];
        }
        return $map;
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
    }

    function buildDictionaries()
    {
    }

    protected function itemCreated( $mapping, $class, $data, $result )
    {
        $url = $result['review_request']['links']['draft']['href'];
        $this->jsonPut($url, $data, array(), true);
    }

    public function buildIdUrl($url, $id)
    {
        if ( strpos($url, '{id}') === false ) {
            return $this->parseUrl($url) . '/' . $id . '/';
        }
        return parent::buildIdUrl($url, $id);
    }
}