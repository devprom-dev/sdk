<?php

abstract class IntegrationRestAPIChannel extends IntegrationChannel
{
    abstract protected function buildUsersMap();
    abstract protected function getUserEmailAttribute();

    public function readItem($mapping, $class, $id, $parms = array())
    {
        $data = $this->mapToInternal( $class, $id,
            array_merge(
                $this->jsonGet($this->buildIdUrl(rtrim($mapping['url'],'/'),$id), array('expand' => 'renderedBody,renderedFields')),
                $parms
            ),
            $mapping,
            function($value, $attribute) {
                $attribute_path =
                    array_map(
                        function($item) {
                            return stripslashes($item);
                        },
                        preg_split('~\\\\.(*SKIP)(*FAIL)|\.~s',$attribute)
                    );
                foreach( $attribute_path as $field ) {
                    list($field, $shift) = preg_split('/:/', $field);
                    if ( $shift == 'first' ) {
                        $value = array_shift($value[$field]);
                    }
                    elseif( is_numeric($shift) && $shift >= 0 ) {
                        $value = $value[$field][intval($shift)];
                    }
                    elseif( $shift != '' ) {
                        list($key, $keyValue) = preg_split('/=/', trim($shift, '()'));
                        $value = array_shift(
                            array_filter($value[$field],
                                function($item) use($key, $keyValue) {
                                    return $item[$key] == $keyValue;
                                }
                            )
                        );
                    }
                    elseif( strpos($field, 'join(') !== false ) {
                        $field = trim(str_replace('join', '', $field),'()');
                        $value = join(',',$value[$field]);
                    }
                    else {
                        $value = $value[$field];
                    }
                }
                return $value;
            }
        );

        return $data;
    }

    public function writeItem($mapping, $class, $id, $data, $queueItem)
    {
        $emails_map = array_flip($this->getUsersMap());

        $put = $this->mapFromInternal( $class, $id, $data, $mapping,
            function($attribute, $value) use($emails_map)
            {
                $attribute_path =
                    array_map(
                        function($item) {
                            return stripslashes($item);
                        },
                        preg_split('~\\\\.(*SKIP)(*FAIL)|\.~s',$attribute)
                    );
                foreach( array_reverse($attribute_path) as $field ) {
                    list($field, $shift) = preg_split('/:/', $field);
                    if ( $shift == 'first' ) {
                        if ( count(array_filter($value, function($v) { return $v != ''; })) < 1 ) {
                            // skip empty value object
                            return array();
                        }
                        $value = array( $field => array($value) );
                    }
                    else if ( $shift == 'intval' ) {
                        $value = array( $field => intval($value) );
                    }
                    else if( $shift != '' ) {
                        list($key, $keyValue) = preg_split('/=/', trim($shift, '()'));
                        $value = array( $field => array(array_merge(array( $key => $keyValue), $value)) );
                    }
                    elseif( strpos($field, 'join(') !== false ) {
                        $field = trim(str_replace('join', '', $field),'()');
                        $value = array( $field => explode(',', $value) );
                    }
                    else {
                        $value = array( $field => $value );
                    }
                    if ( $field == $this->getUserEmailAttribute() && $emails_map[$value[$field]] != '' ) {
                        $value['name'] = $emails_map[$value[$field]];
                    }
                }
                return $value;
            }
        );

        $result = array();
        if ( is_subclass_of($class, 'Attachment') )
        {
            if ( $id == '' ) {
                $result = $this->filePost(
                    $this->getObjectIt()->get('URL').$mapping['url-append'],
                    $data['FilePath'],
                    $data['FileExt'],
                    $data['FileMime']
                );
            }
        }
        else {
            if ( $id != '' ) {
                if ( $class == 'RequestLink' ) return $result;
                $result = array (
                    $this->jsonPut($this->buildIdUrl(rtrim($mapping['url'],'/'), $id), $put, array('expand' => 'renderedBody'))
                );
            }
            else {
                $postData = $put;
                $result = $this->jsonPost(
                    $mapping['url-append'] != '' ? $mapping['url-append'] : $mapping['url'],
                    $postData,
                    array('expand' => 'renderedBody')
                );
                $this->itemCreated($mapping, $class, $put, $result);
                $result = array($result);
            }
        }
        return $result;
    }

    protected function itemCreated( $mapping, $class, $data, $result )
    {
    }

    public function deleteItem($mapping, $class, $id)
    {
        try {
            return $this->jsonDelete($this->buildIdUrl(rtrim($mapping['url'],'/'),$id), array('deleteSubtasks' => 'true'));
        }
        catch( Exception $e ) {
            $this->getLogger()->error($e->getMessage().PHP_EOL.$e->getTraceAsString());
            return array();
        }
    }

    public function getUsersMap() {
        if ( count($this->usersMap) < 1 ) {
            $this->usersMap = $this->buildUsersMap();
        }
        return $this->usersMap;
    }

    public function getWebLink( $id, $data, $link_pattern ) {
        $parentId = $data['{parent}'];
        if ( $parentId == '' ) $parentId = $data['{parentId}'];
        return $this->buildIdUrl(
            $this->getObjectIt()->get('URL') .
                    preg_replace('/\{parent\}/', $parentId,
                        preg_replace('/\{parentId\}/', $parentId, $link_pattern)), $id
        );
    }

    private $usersMap = array();
}