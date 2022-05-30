<?php

class IntegrationYouTrackChannel extends IntegrationRestAPIChannel
{
    private $cookiesFile = '';

    function __construct($object_it, $logger = null)
    {
        $this->cookiesFile = tempnam(sys_get_temp_dir(), 'youtrack_cookies');
        parent::__construct($object_it, $logger);
    }

    function __destruct()
    {
        parent::__destruct();
        unlink($this->cookiesFile);
    }

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array(
            'max' => $limit,
            'query' => "project: {$this->getObjectIt()->get('ProjectKey')}"
        );
        if ( $timestamp != '' ) {
            $time = new DateTime($timestamp, new DateTimeZone("UTC"));
            $time->modify("+1 second");
            $timestamp = array_shift(preg_split('/\./', $time->format(DateTime::RFC3339_EXTENDED)));
            $jql['query'] .= " updated: {$timestamp} .. Tomorrow";
        }

        $releases = array();
        $issues = array();
        $hours = array();
        $nextTimestamp = '';

        try {
            $result = $this->jsonGet("/api/issues?fields=id,summary,updated&", $jql);
            foreach( $result as $issue )
            {
                $class = 'Request';
                $id = $issue[$this->getKeyField()];
                $item = $issues[$id] = array (
                    'class' => $class,
                    'id' => $id
                );
                $issues = array_merge( $issues,
                    $this->getReferenceItems($issue, $item, $timestamp)
                );

                try {
                    $resultAttachment = $this->jsonGet('/api/issues/' . $id . '/attachments?fields=id,fileUrl');
                    foreach ($resultAttachment['fileUrl'] as $attachmentItem) {
                        if (!$this->checkNewItem($timestamp, $attachmentItem)) continue; // skip non-modified items
                        $issues[] = array(
                            'class' => 'RequestAttachment',
                            'id' => $attachmentItem[$this->getKeyField()],
                            'parentId' => $id
                        );
                    }
                }
                catch( \Exception $e ) {
                }

                try {
                    $resultTime = $this->jsonGet('/api/issues/'.$id.'/timeTracking/workItems?fields=id');
                    foreach( $resultTime as $timeItem ) {
                        if ( !$this->checkNewItem($timestamp, $timeItem) ) continue; // skip non-modified items
                        $issues[] = array (
                            'class' => 'ActivityRequest',
                            'id' => $timeItem[$this->getKeyField()],
                            'parentId' => $id
                        );
                    }
                }
                catch( \Exception $e ) {
                }

                if ( $issue['field']['updated']['value'] > $nextTimestamp ) $nextTimestamp = $issue['field']['updated']['value'];
            }
        }
        catch( \Exception $e ) {
        }

        return array(
            array_merge(
                $releases, $issues, $hours
            ),
            $nextTimestamp != '' ? new \DateTime(date('Y-m-d h:i:s', round(floatval($nextTimestamp) / 1000, 0)), new DateTimeZone("UTC")) : ''
        );
    }

    protected function getUserEmailAttribute() {
        return 'email';
    }

    protected function getHeaders()
    {
        return array (
            "Accept: application/json"
        );
    }

    public function mapToInternal($class, $id, $source, $mapping, $getter)
    {
        $data = parent::mapToInternal($class, $id, $source, $mapping, $getter);

        foreach( $data as $attribute => $value ) {
            if ( $attribute == 'File' ) {
                $binaryData = $this->binaryGet($value);
                $data[$attribute] = $this->convertToFileAttribute($binaryData);
                $finfo = new \finfo(FILEINFO_MIME);
                $data['FileMime'] = $finfo->buffer($binaryData);
            }
            if ( $attribute == 'Capacity' ) {
                $data[$attribute] = $data[$attribute] / 60;
            }
            if ( in_array($attribute, array('ReportDate','RecordCreated','RecordModified')) ) {
                $data[$attribute] = date('Y-m-d h:i:s', round(floatval($data[$attribute]) / 1000, 0));
            }
        }

        return $data;
    }

    public function mapFromInternal($class, $id, $source, $mapping, $setter)
    {
        $put = parent::mapFromInternal($class, $id, $source, $mapping, $setter);

        foreach( $source as $attribute => $value ) {
            if ( $attribute == 'Capacity' ) {
                $source[$attribute] = round($source[$attribute] * 60, 0);
            }
            if ( in_array($attribute, array('ReportDate','RecordCreated','RecordModified')) ) {
                $source[$attribute] = strtotime($source[$attribute]) * 1000;
            }
        }

        return $put;
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
        return array();
    }

    function buildDictionaries()
    {
        $stateResult = $this->jsonGet('/api/admin/customfield/stateBundle/States', array(), false);
        foreach( $stateResult['state'] as $issueState ) {
            $this->issueStates[$issueState['value']] = $issueState['value'];
        }
    }

    protected function buildUsersMap()
    {
        $map = array();
        $users = $this->jsonGet(
            '/api/admin/user',
            array(),
            false
        );
        foreach( $users as $user ) {
            $userInfo = $this->jsonGet('/api/admin/user/'.$user['login'], array(), false );
            $map[$userInfo['login']] = $user[$this->getUserEmailAttribute()];
        }
        return $map;
    }

    protected function checkMappedValueExists( $fieldName, $value )
    {
        switch( $fieldName ) {
            case 'field.Stage.value':
            case 'field.State.value':
                return count($this->issueStates) < 2 || in_array($value, $this->issueStates);
            default:
                return true;
        }
    }

    protected function checkNewItem( $timestamp, $item )
    {
        if ( $timestamp != '' && $item['created'] != '' && strtotime($timestamp) * 1000 > floatval($item['created']) ) return false;
        return true;
    }

    protected function buildCurl()
    {
        if ( $this->getObjectIt()->get('HttpUserName') == '' ) return parent::buildCurl();

        $loginCurl = CurlBuilder::getCurl();
        $loginParms = array(
            'login' => $this->getObjectIt()->get('HttpUserName'),
            'password' => $this->getObjectIt()->get('HttpUserPassword')
        );

        curl_setopt($loginCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($loginCurl, CURLOPT_HEADER, false);
        curl_setopt($loginCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($loginCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($loginCurl, CURLOPT_COOKIEJAR, $this->cookiesFile);
        curl_setopt($loginCurl, CURLOPT_POSTFIELDS, http_build_query($loginParms));
        curl_setopt($loginCurl, CURLOPT_URL, rtrim($this->getObjectIt()->get('URL'),'\\/').'/youtrack/api/rest/user/login');
        curl_setopt($loginCurl, CURLOPT_PUT, true);
        curl_setopt($loginCurl, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($loginCurl);
        if ( $result === false ) {
            $message = curl_error($loginCurl);
            curl_close($loginCurl);
            throw new Exception($message);
        }

        $info = curl_getinfo($loginCurl);
        if ( $info['http_code'] >= 300 ) {
            $this->getLogger()->error(var_export($info,true));
            $this->getLogger()->error($result);
            curl_close($loginCurl);
            throw new Exception('Http status code: '.$info['http_code']);
        }
        curl_close($loginCurl);

        return parent::buildCurl();
    }

    protected function buildAuthParms( $curl ) {
        curl_setopt ($curl, CURLOPT_COOKIEFILE, $this->cookiesFile);
    }

    protected function parseJsonResult()
    {
        $data = parent::parseJsonResult();
        if ( !is_array($data) ) return $data;
        return $this->normalizeJson($data);
    }

    protected function normalizeJson( $data )
    {
        foreach($data as $rowId => $rowData) {
            if ( sprintf('%s',$rowId) == 'field' ) {
                $data[$rowId] = $this->convertFields($rowData);
            }
            else {
                if ( is_array($rowData) ) {
                    $data[$rowId] = $this->normalizeJson($rowData);
                }
            }
        }
        return $data;
    }

    protected function convertFields( $data )
    {
        $fields = array();
        foreach( $data as $fieldKey => $fieldData ) {
            $fields[$fieldData['name']] = $fieldData;
        }
        return $fields;
    }

    private $issueTypeMap = array();
    private $issueStates = array();
    private $taskIssueType = 0;
}