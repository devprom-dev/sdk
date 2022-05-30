<?php

abstract class IntegrationChannel
{
    public function __construct( $object_it, $logger = null )
    {
        $this->object_it = $object_it;
        $this->logger = $logger;
        $this->curl = $this->buildCurl();
    }

    function __destruct() {
        curl_close($this->curl);
    }

    public function getTimestamp() {
        $time = new DateTime("now", new DateTimeZone("UTC"));
        return $time->format('Y-m-d H:i:s');
    }

    protected function getLogger() {
        return $this->logger;
    }

    public function setCurlDelay( $delay ) {
        $this->curlDelay = $delay;
    }

    public function getMapping() {
        return $this->mapping;
    }

    public function setMapping( $mapping ) {
        $this->mapping = $mapping;
    }

    public function setIdsMap( $map, $backward ) {
        $this->idsMapWrite = $map;
        $this->idsMapRead = $backward;
    }

    protected function getIdsMapRead() {
        return $this->idsMapRead;
    }

    protected function getIdsMapWrite() {
        return $this->idsMapWrite;
    }

    protected function getObjectIt() {
        return $this->object_it;
    }

    public function getKeyField() {
        return 'id';
    }

    public function getKeyValue( $data ) {
        return $data[$this->getKeyField()];
    }

    public function getSearchUrl( $ids ) {
        return '';
    }

    protected function getHeaders() {
        return array();
    }

    protected function getEscapedUrl( $url ) {
        return str_replace(' ', '%20', $url);
    }

    protected function binaryGet( $url, $data = array() )
    {
        $location = $this->getEscapedUrl($url)
            . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($data);

        curl_setopt($this->curl, CURLOPT_URL, $location);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "GET");

        return $this->parseResult();
    }

    protected function binaryPost( $url, $content )
    {
        $location = $this->getEscapedUrl($url);
        curl_setopt($this->curl, CURLOPT_URL, $location);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $content);

        return $this->parseResult();
    }

    protected function filePost( $url, $path, $name, $mimeType )
    {
        if (function_exists('curl_file_create')) {
            $fileInfo = curl_file_create($path, $mimeType, $name);
        }
        else {
            $fileInfo = "@".$path.";filename=" . $name;
            if ($mimeType != '') {
                $fileInfo .= ';type=' . $mimeType;
            }
        }
        $content = array (
            'file' => $fileInfo
        );

        $location = $this->getEscapedUrl($url);
        curl_setopt($this->curl, CURLOPT_URL, $location);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $content);

        return $this->parseJsonResult();
    }

    protected function jsonGet( $url, $data = array(), $verbose = true )
    {
        if ( $this->curlDelay > 0 ) sleep($this->curlDelay);

        $url = $this->getEscapedUrl($this->object_it->get('URL') . $url);
        $url .= (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($data);

        $this->getLogger()->info("GET: ".$url);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "GET");

        $data = $this->parseJsonResult();
        if ( $verbose ) {
            $this->getLogger()->debug("jsonGet: ".var_export($data,true));
        }

        return $data;
    }

    protected function jsonPost( $url, $post = array(), $parms = array(), $verbose = true )
    {
        $url = $this->getEscapedUrl($this->object_it->get('URL') . $url);
        $post = $this->buildPostFields($post);

        $url .= (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($parms);
        $this->getLogger()->info('POST: ' . $url);

        if ( $verbose ) {
            $this->getLogger()->debug('jsonPost data: '.var_export($post,true));
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            array_merge(
                $this->buildHeaders(),
                $this->getPostHeaders($url)
            )
        );

        $data = $this->parseJsonResult();
        if ( $verbose ) {
            $this->getLogger()->debug('jsonPost result: ' . var_export($data, true));
        }

        return $data;
    }

    protected function getPostHeaders( $url ) {
        return array(
            "Content-Type: application/json"
        );
    }

    protected function buildPostFields( $post ) {
        // application/x-www-form-urlencoded
        return json_encode($post);
    }

    protected function jsonPut( $url, $post = array(), $parms = array(), $verbose = true )
    {
        if ( strpos($url, 'http') === false ) {
            $url = $this->getEscapedUrl($this->object_it->get('URL') . $url);
        }
        $post = $this->buildPostFields($post);

        $url .= (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($parms);

        $this->getLogger()->info('PUT: '.$url);
        if ( $verbose ) {
            $this->getLogger()->debug(var_export($post,true));
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            array_merge(
                $this->buildHeaders(),
                $this->getPutHeaders()
            )
        );

        $data = $this->parseJsonResult();
        if ( $verbose ) {
            $this->getLogger()->debug('jsonPut result: ' . var_export($data, true));
        }

        return $data;
    }

    protected function getPutHeaders() {
        return array(
            "Content-Type: application/json"
        );
    }

    protected function jsonPatch( $url, $post = array(), $parms = array(), $verbose = true )
    {
        if ( strpos($url, 'http') === false ) {
            $url = $this->object_it->get('URL').$url;
        }
        $post = $this->buildPatchFields($post);

        $url .= (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($parms);

        $this->getLogger()->info('PATCH: '.$url);
        if ( $verbose ) {
            $this->getLogger()->debug(var_export($post,true));
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            array_merge(
                $this->buildHeaders(),
                $this->getPatchHeaders()
            )
        );

        $data = $this->parseJsonResult();
        if ( $verbose ) {
            $this->getLogger()->debug('jsonPatch result: ' . var_export($data, true));
        }

        return $data;
    }

    protected function getPatchHeaders() {
        return array(
            "Content-Type: application/json-patch+json"
        );
    }

    private function getPatchOperations( $path, $array ) {
        $data = array();
        foreach( $array as $key => $value ) {
            if ( is_array($value) ) {
                $data = array_merge( $data,
                    $this->getPatchOperations($path . '/' . $key, $value)
                );
            }
            else {
                if ( in_array($key, array('readonly','writeonly')) ) continue;
                if ( $value == '' ) {
                    $data[] = array(
                        'op' => 'remove',
                        'path' => $path . '/' . $key
                    );
                }
                else {
                    $data[] = array(
                        'op' => 'add',
                        'path' => $path . '/' . $key,
                        'value' => $value
                    );
                }
            }

        }
        return $data;
    }

    protected function buildPatchFields( $post ) {
        $data = $this->getPatchOperations('', $post);
        return json_encode($data);
    }

    protected function jsonDelete( $url, $parms = array() )
    {
        $url = $this->getEscapedUrl($this->object_it->get('URL') . $url);
        $url .= (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($parms);
        $this->getLogger()->info('DELETE: '.$url);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");

        $result = $this->parseResult();
        $this->getLogger()->debug('jsonDelete result: '.var_export($result,true));

        return $result;
    }

    private function parseResult()
    {
        $result = curl_exec($this->curl);
        if ( $result === false ) {
            throw new Exception("curl_exec failed: "
                . curl_errno($this->curl) . ", " . curl_error($this->curl));
        }

        $info = curl_getinfo($this->curl);
        if ( $info['http_code'] >= 300 ) {
            $this->getLogger()->error(var_export($info,true));
            $this->getLogger()->error($result);
            throw new Exception('Http status code: '.$info['http_code']);
        }
        return $result;
    }

    protected function parseJsonResult()
    {
        $result = $this->parseResult();
        if ( $result == "" ) return array();

        $data = JsonWrapper::decode($result);
        if ( !is_array($data) ) throw new Exception($result);

        return $data;
    }

    protected function buildCurl()
    {
        $curl = CurlBuilder::getCurl();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_REFERER, EnvironmentSettings::getServerUrl());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->buildHeaders());
        $this->buildAuthParms($curl);
        return $curl;
    }

    protected function buildHeaders()
    {
        return array_merge( $this->getHeaders(),
            preg_split('/[\r\n]+/i', $this->object_it->getHtmlDecoded('HttpHeaders'))
        );
    }

    protected function buildAuthParms( $curl )
    {
        if ( $this->object_it->get('HttpUserName') != '' ) {
            curl_setopt($curl, CURLOPT_USERPWD,
                $this->object_it->getHtmlDecoded('HttpUserName') . ":" . $this->object_it->getHtmlDecoded('HttpUserPassword'));
        }
    }

    protected function mapToInternal( $class, $id, $source, $mapping, $getter )
    {
        $data = array();
        $object = getFactory()->getObject($class);

        foreach( $mapping as $attribute => $column )
        {
            if ( in_array($attribute, array('url','link','url-append','originalAppendUrl')) ) continue;
            if ( is_array($column) ) {
                if ( $column['writeonly'] ) continue;

                $value = $this->mapToInternal($class, $id, $source, $column, $getter);
                if ( $column['type'] != '' ) {
                    $value = $value['reference'];
                    $id = is_array($value) ? $value[$this->getKeyField()] : $value;
                    if ( $id == '' ) continue; // skip one-to-many reference
                    // process one-to-one reference
                    $data[$attribute] = $this->idsMapRead[$column['type'].$id];
                    if ( $data[$attribute] == '' ) {
                        $data[$attribute] = $this->idsMapRead[$value[$this->getKeyField()]];
                    }
                }
                else {
                    if ( is_array($column['mapping']) ) {
                        $mappingField = array_shift(array_keys($value));
                        $mapped = false;
                        foreach( $column['mapping'] as $field_mapping ) {
                            $internal = array_pop(array_keys($field_mapping));
                            $native = array_pop($field_mapping);
                            if ( $value[$mappingField] == $native || $native == "*" ) {
                                if ( $mappingField == "." ) {
                                    $value = $internal;
                                }
                                else {
                                    $value[$mappingField] = $internal;
                                }
                                $mapped = true;
                                break;
                            }
                        }
                        if ( !$mapped ) {
                            throw new Exception(
                                sprintf(
                                    "Skip import record because of mapping has not been resolved for the attribute: %s\nMapping is: %s\nValue is: %s",
                                    $attribute, var_export($column['mapping'], true), var_export($value[$mappingField], true)
                                )
                            );
                        }
                    }
                    else {
                        if ( $column == '{parentId}' && $value != '' ) {
                            $value = array(
                                'Id' => $value
                            );
                        }
                        elseif ( $object->IsReference($attribute) ) {
                            if ( is_numeric($value) && $value > 0 ) {
                                $value = array(
                                    $attribute => $this->idsMapRead[get_class($object->getAttributeObject($attribute)).$value]
                                );
                            }
                        }
                        elseif ( is_array($value) ) {
                            $value = array_shift(array_values($value));
                        }
                    }

                    $data[$attribute] = $value;
                }
            }
            else {
                $value = call_user_func($getter, $source, trim(array_shift(preg_split('/,/',$column))));

                if ( $column == '{parentId}' && $value != '' ) {
                    $value = array(
                        'Id' => $value
                    );
                }
                else if ( $object->IsReference($attribute) && $value != '' ) {
                    $value = array(
                        'Id' => $this->idsMapRead[get_class($object->getAttributeObject($attribute)).$value]
                    );
                }
                $data[$attribute] = $value;
            }
        }

        return $data;
    }

    protected function mapFromInternal( $class, $id, $source, $mapping, $setter )
    {
        $data = array();
        $object = getFactory()->getObject($class);

        foreach( $mapping as $attribute => $column )
        {
            if ( in_array($attribute, array('url','link','url-append','originalAppendUrl')) ) continue;
            if ( in_array($column, array('{parent}','{parentId}')) ) continue;
            if ( is_array($column) )
            {
                if ( $column['readonly'] ) continue;
                if ( $column['type'] != '' )
                {
                    if ( $column['reference'] == '' || $source[$attribute]['Id'] == '' ) continue;
                    $idValue = $this->idsMapWrite[$column['type'].$source[$attribute]['Id']];
                    if ( $idValue == '' ) continue;
                    $source[$attribute] = array (
                        $this->getKeyField() => $idValue
                    );
                    $value = call_user_func($setter, $column['reference'], $source[$attribute]);
                }
                else {
                    if ( is_array($column['mapping']) ) {
                        $mappingField = array_shift(array_keys($column));
                        $nativeField = $column[$mappingField];
                        $mapped = false;

                        foreach( $column['mapping'] as $field_mapping ) {
                            $internal = array_pop(array_keys($field_mapping));
                            $native = array_pop($field_mapping);

                            if ( $native == '*' ) {
                                unset($source[$attribute]);
                                $mapped = true;
                                continue;
                            }
                            if ( !$this->checkMappedValueExists($nativeField, $native) ) continue;

                            if ( $mappingField == "." ) {
                                if ( $internal == $source[$attribute] || is_array($source[$attribute]) && $internal == $source[$attribute]["Id"] ) {
                                    $source[$attribute] = array ( $mappingField => $native );
                                    $mapped = true;
                                    break;
                                }
                            }
                            else {
                                if ( $internal == $source[$attribute][$mappingField] ) {
                                    $source[$attribute][$mappingField] = $native;
                                    $mapped = true;
                                    break;
                                }
                            }
                        }
                        if ( !$mapped ) {
                            throw new Exception(
                                sprintf(
                                    "Skip export record because of mapping has not been resolved for the attribute: %s\nMapping is: %s\nValue is: %s",
                                    $attribute, var_export($column['mapping'], true), var_export($source[$attribute], true)
                                )
                            );
                        }
                        unset($column['mapping']);
                    }

                    $value = $this->mapFromInternal($class, $id, $source[$attribute], $column, $setter);

                    if ( $object->IsReference($attribute) && $source[$attribute]['Id'] != '' ) {
                        $columnKey = array_shift(array_keys($value));
                        $idValue = $this->idsMapWrite[get_class($object->getAttributeObject($attribute)).$source[$attribute]['Id']];
                        if ( $idValue != '' ) {
                            $value[$columnKey][$this->getKeyField()] = $idValue;
                        }
                    }
                }
            }
            else {
                $value = array();
                foreach( preg_split('/,/', $column) as $int_column ) {
                    $attributeType = $object->getAttributeType($attribute);
                    $strValue = strval($source[$attribute]);
                    if ( $strValue != '' ) {
                        switch( $attributeType ) {
                            case 'date':
                            case 'datetime':
                                $dt = new DateTime($strValue, new DateTimeZone('UTC'));
                                $strValue = $dt->format(DateTime::ISO8601);
                                break;
                        }
                    }
                    $value = array_merge_recursive($value, call_user_func($setter, trim($int_column), $strValue));
                }
            }
            $data = array_merge_recursive($data, $value);
        }
        return $data;
    }

    protected function getReferenceItems( $parentItem, $queueItem, $internalTimeStamp )
    {
        $mapping = $this->getMapping();
        $result = array();

        // append references into the items queue
        foreach( $mapping[$queueItem['class']] as $attribute => $column )
        {
            if ( !is_array($column) ) continue;
            if ( $column['type'] == '' || $column['reference'] == '' ) continue;

            $attribute_path = preg_split('/\./',$column['reference']);
            $value = $parentItem[array_shift($attribute_path)];
            foreach( $attribute_path as $field ) $value = $value[$field];

            $parentId = $queueItem[$this->getKeyField()];
            if ( $parentId != '' && $queueItem['class'] == 'RequestLink' ) {
                $result[] = array (
                    'class' => $queueItem['class'],
                    'id' => $parentId
                );
            }

            if ( $value[$this->getKeyField()] == '' ) {
                // one-to-many
                foreach( $value as $item ) {
                    if ( !$this->checkNewItem($internalTimeStamp, $item) ) continue; // skip non-modified items
                    $result[] = array (
                        'class' => $column['type'],
                        'id' => $item[$this->getKeyField()],
                        'parentId' => $parentId
                    );
                }
            }
            else {
                // one-to-one
                if ( !$this->checkNewItem($internalTimeStamp, $value) ) continue; // skip non-modified items
                $result[$value[$this->getKeyField()]] = array (
                    'class' => $column['type'],
                    'id' => $value[$this->getKeyField()],
                    'parentId' => $parentId
                );
            }
        }
        return $result;
    }

    protected function convertToFileAttribute( $content ) {
        return base64_encode($content);
    }

    protected function convertFromFileAttribute( $data ) {
        return base64_decode($data);
    }

    protected function checkMappedValueExists( $fieldName, $value ) {
        return true;
    }

    protected function checkNewItem( $timestamp, $item ) {
        return true;
    }

    public function parseUrl( $url ) {
        return str_replace('{project}', $this->getObjectIt()->get('ProjectKey'), $url);
    }

    public function buildIdUrl($url, $id)
    {
        if ( strpos($url, '{'.$this->getKeyField().'}') === false ) {
            return $this->parseUrl($url) . '/' . $id;
        }
        return str_replace('{'.$this->getKeyField().'}', $id, $this->parseUrl($url));
    }

    public function setWysiwygMode( $value ) {
        $this->wysiwygMode = $value;
    }

    public function getWysiwygMode() {
        return $this->wysiwygMode;
    }

    abstract public function buildDictionaries();
    abstract public function getItems( $timestamp, $limit );
    abstract public function readItem( $mapping, $class, $id, $parms = array() );
    abstract public function writeItem( $mapping, $class, $id, $data, $queueItem );
    abstract public function deleteItem( $mapping, $class, $id );
    abstract public function storeLink( $mapping, $class, $id, $link, $title );

    private $logger = null;
    private $object_it = null;
    private $curl = null;
    private $idsMapRead = array();
    private $idsMapWrite = array();
    private $mapping = array();
    private $curlDelay = 0;
    private $wysiwygMode = false;
}