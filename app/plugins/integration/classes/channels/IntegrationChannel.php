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
        return SystemDateTime::date();
    }

    protected function getLogger() {
        return $this->logger;
    }

    public function getMapping() {
        return $this->mapping;
    }

    public function setMapping( $mapping ) {
        $this->mapping = $mapping;
    }

    public function setIdsMap( $map ) {
        $this->idsMapRead = $map;
        $this->idsMapWrite = array_flip($map);
    }

    public function setIdsMapReversed( $map ) {
        $this->idsMapWrite = $map;
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

    protected function binaryGet( $url, $data = array() )
    {
        curl_setopt($this->curl, CURLOPT_URL, $url.(strpos($url, '?') === FALSE ? '?' : '').http_build_query($data));
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "GET");

        return $this->parseResult();
    }

    protected function binaryPost( $url, $content )
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
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
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            array_merge(
                $this->getHeaders(),
                preg_split('/[\r\n]+/i', $this->object_it->getHtmlDecoded('HttpHeaders'))
            )
        );
        return $this->parseJsonResult();
    }

    protected function jsonGet( $url, $data = array(), $verbose = true )
    {
        $url = $this->object_it->get('URL').$url;
        $url .= (strpos($url, '?') === FALSE ? '?' : '').http_build_query($data);
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
        $url = $this->object_it->get('URL').$url;
        $post = $this->buildPostFields($post);

        $this->getLogger()->info('POST: '.$url);
        if ( $verbose ) {
            $this->getLogger()->debug('jsonPost data: '.var_export($post,true));
        }

        curl_setopt($this->curl, CURLOPT_URL, $url.(strpos($url, '?') === FALSE ? '?' : '').http_build_query($parms));
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

        $data = $this->parseJsonResult();
        if ( $verbose ) {
            $this->getLogger()->debug('jsonPost result: ' . var_export($data, true));
        }

        return $data;
    }

    protected function buildPostFields( $post ) {
        // application/x-www-form-urlencoded
        return json_encode($post);
    }

    protected function jsonPut( $url, $post = array(), $parms = array(), $verbose = true )
    {
        if ( strpos($url, 'http') === false ) {
            $url = $this->object_it->get('URL').$url;
        }
        $post = $this->buildPostFields($post);

        $this->getLogger()->info('PUT: '.$url);
        if ( $verbose ) {
            $this->getLogger()->debug(var_export($post,true));
        }

        curl_setopt($this->curl, CURLOPT_URL, $url.(strpos($url, '?') === FALSE ? '?' : '').http_build_query($parms));
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

        $data = $this->parseJsonResult();
        if ( $verbose ) {
            $this->getLogger()->debug('jsonPut result: ' . var_export($data, true));
        }

        return $data;
    }

    protected function jsonDelete( $url, $parms = array() )
    {
        $url = $this->object_it->get('URL').$url;
        $this->getLogger()->info('DELETE: '.$url);

        curl_setopt($this->curl, CURLOPT_URL, $url.(strpos($url, '?') === FALSE ? '?' : '').http_build_query($parms));
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
        if ( $result === false ) throw new Exception(curl_error($this->curl));

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
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array_merge(
                $this->getHeaders(),
                preg_split('/[\r\n]+/i', $this->object_it->getHtmlDecoded('HttpHeaders'))
            )
        );
        $this->buildAuthParms($curl);

        return $curl;
    }

    protected function buildAuthParms( $curl )
    {
        if ( $this->object_it->get('HttpUserName') != '' ) {
            curl_setopt($curl, CURLOPT_USERPWD,
                $this->object_it->getHtmlDecoded('HttpUserName') . ":" . $this->object_it->getHtmlDecoded('HttpUserPassword'));
        }
    }

    protected function mapToInternal( $source, $mapping, $getter )
    {
        $data = array();
        foreach( $mapping as $attribute => $column )
        {
            if ( in_array($attribute, array('url','link','url-append','originalUrl','originalAppendUrl')) ) continue;
            if ( is_array($column) ) {
                if ( $column['writeonly'] ) continue;

                $value = $this->mapToInternal($source, $column, $getter);
                if ( $column['type'] != '' ) {
                    $value = $value['reference'];
                    $id = $value[$this->getKeyField()];
                    if ( $id == '' ) continue; // skip one-to-many reference
                    // process one-to-one reference
                    $data[$attribute] = $this->idsMapRead[$id];
                    if ( $data[$attribute] == '' ) {
                        $data[$attribute] = $this->idsMapRead[$value['key']];
                    }
                }
                else {
                    if ( is_array($column['mapping']) )
                    {
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
                    $data[$attribute] = $value;
                }
            }
            else {
                $data[$attribute] = call_user_func($getter, $source, trim(array_shift(preg_split('/,/',$column))));
            }
        }
        return $data;
    }

    protected function mapFromInternal( $source, $mapping, $setter )
    {
        $data = array();

        foreach( $mapping as $attribute => $column )
        {
            if ( in_array($attribute, array('url','link','url-append','originalUrl','originalAppendUrl')) ) continue;
            if ( in_array($column, array('{parent}','{parentId}')) ) continue;
            if ( is_array($column) )
            {
                if ( $column['readonly'] ) continue;
                if ( $column['type'] != '' )
                {
                    if ( $column['reference'] == '' || $source[$attribute]['Id'] == '' ) continue;
                    $source[$attribute] = array (
                        'key' => $this->idsMapWrite[$column['type'].$source[$attribute]['Id']]
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

                            if ( !$this->checkMappedValueExists($nativeField, $native) ) continue;

                            if ( $mappingField == "." ) {
                                if ( $native == "*" || $internal == $source[$attribute] || is_array($source[$attribute]) && $internal == $source[$attribute]["Id"] ) {
                                    $source[$attribute] = array ( $mappingField => $native );
                                    $mapped = true;
                                    break;
                                }
                            }
                            else {
                                if ( $internal == $source[$attribute][$mappingField] || $native == "*" ) {
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
                    $value = $this->mapFromInternal($source[$attribute], $column, $setter);
                }
            }
            else {
                $value = array();
                foreach( preg_split('/,/', $column) as $int_column ) {
                    $value = array_merge_recursive($value, call_user_func($setter, trim($int_column), strval($source[$attribute])));
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

            if ( $value[$this->getKeyField()] == '' ) {
                // one-to-many
                foreach( $value as $item ) {
                    if ( !$this->checkNewItem($internalTimeStamp, $item) ) continue; // skip non-modified items
                    $result[] = array (
                        'class' => $column['type'],
                        'id' => $item[$this->getKeyField()],
                        'parentId' => $queueItem[$this->getKeyField()]
                    );
                }
            }
            else {
                // one-to-one
                if ( !$this->checkNewItem($internalTimeStamp, $value) ) continue; // skip non-modified items
                if ( $value['key'] != '' ) $value['id'] = $value['key'];
                $id = $value[$this->getKeyField()];
                $result[$id] = array (
                    'class' => $column['type'],
                    'id' => $id,
                    'parentId' => $queueItem[$this->getKeyField()]
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
}