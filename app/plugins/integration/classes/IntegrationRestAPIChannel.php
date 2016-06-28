<?php

abstract class IntegrationRestAPIChannel extends IntegrationChannel
{
    abstract protected function buildUsersMap();
    abstract protected function buildIdUrl( $url, $id );
    abstract protected function getUserEmailAttribute();

    public function readItem($mapping, $class, $id, $parms = array())
    {
        $data = $this->mapToInternal(
            array_merge(
                $this->jsonGet($this->buildIdUrl(rtrim($mapping['url'],'/'),$id), array('expand' => 'renderedBody,renderedFields')),
                $parms
            ),
            $mapping,
            function($data, $attribute) {
                $attribute_path = preg_split('/\./',$attribute);
                $value = $data[array_shift($attribute_path)];
                foreach( $attribute_path as $field ) {
                    list($field, $shift) = preg_split('/:/', $field);
                    if ( $shift == 'first' ) {
                        $value = array_shift($value[$field]);
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

    public function writeItem($mapping, $class, $id, $data)
    {
        $emails_map = array_flip($this->getUsersMap());

        $put = $this->mapFromInternal( $data, $mapping,
            function($attribute, $value) use($emails_map)
            {
                $attribute_path = preg_split('/\./',$attribute);
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
                    else {
                        $value = array( $field => $value );
                    }
                    if ( $field == $this->getUserEmailAttribute() ) {
                        $value['name'] = $emails_map[$value[$field]];
                        if ( $value['name'] == '' ) return array();
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
                $result = $this->jsonPost($mapping['url'], $postData, array('expand' => 'renderedBody'));
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

    private $usersMap = array();
}