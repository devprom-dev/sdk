<?php
use Frlnc\Slack\Http\SlackResponseFactory;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Core\Commander;
use Devprom\ProjectBundle\Service\Model\ModelService;

class IntegrationSlackChannel extends IntegrationChannel
{
    function __construct($object_it, $logger)
    {
        parent::__construct($object_it, $logger);

        $interactor = new CurlInteractor;
        $interactor->setResponseFactory(new SlackResponseFactory);

        $this->slack = new Commander($this->getObjectIt()->getHtmlDecoded('ProjectKey'), $interactor);
        $this->channel = $this->getObjectIt()->getHtmlDecoded('URL');
        $this->uidService = new ObjectUID();

        $this->model = new ModelService(
            new \ModelValidator(
                array (
                    new \ModelValidatorObligatory(),
                    new \ModelValidatorTypes()
                )
            ),
            new \ModelDataTypeMapper()
        );
        $this->model->setSkipFields(array('VPD','RecordVersion'));

        $this->defaultPayload = array(
            'username' => $this->getObjectIt()->get('HttpUserName'),
            'as_user' => false,
            'icon_emoji' => ':robot_face:'
        );
    }

    function getTimestamp() {
        return $this->timestamp;
    }

    public function getItems( $timestamp, $limit )
    {
        $this->timestamp = $timestamp;
        $mapping = $this->getMapping();

        $response = $this->slack->execute("channels.list", array());
        $responseBody = $response->getBody();
        $this->getLogger()->info("Slack channels found ".count($responseBody['channels']));

        foreach( $responseBody['channels'] as $channel )
        {
            $historyPayload = array(
                'channel' => $channel['id'],
                'inclusive' => 0
            );
            if ( $timestamp != "" ) {
                $historyPayload['oldest'] = $timestamp;
            }
            $this->getLogger()->info("History query ".var_export($historyPayload,true));
            $historyResponse = $this->slack->execute("channels.history", $historyPayload);
            $historyBody = $historyResponse->getBody();
            $this->getLogger()->info("Slack messages found ".count($historyBody));

            foreach( $historyBody['messages'] as $message )
            {
                $matches = array();
                if ( preg_match('/\[?([A-Z]{1}-[0-9]+)\]?/i', $message['text'], $matches) ) {
                    $wasAttachment = false;
                    $uidFound = $matches[1];
                    foreach($message['attachments'] as $attachment) {
                        if ( $attachment['fallback'] == $uidFound ) {
                            $wasAttachment = true;
                            break;
                        }
                    }
                    if ( !$wasAttachment ) {
                        $object_it = $this->uidService->getObjectIt($uidFound);
                        if ( $object_it->getId() != '' ) {
                            $class = get_class($object_it->object);
                            $objectMapping = $mapping[$class];
                            if ( !is_array($objectMapping) ) {
                                $this->getLogger()->info("There is no mapping for substitution of ".get_class($object_it->object));
                            }
                            else {
                                $updatedMessage = array(
                                    'channel' => $channel['id'],
                                    'text' => text('integration20'),
                                    'attachments' => array(
                                        array(
                                            'ts' => $message['ts'],
                                            'channel_id' => $channel['id'],
                                            'is_msg_unfurl' => true,
                                            'text' => $message['text'],
                                            'author_name' => $this->users[$message['user']],
                                            'mrkdwn_in' =>
                                                array (
                                                    0 => 'text',
                                                ),
                                            'color' => 'D0D0D0',
                                            'is_share' => true
                                        )
                                    )
                                );
                                $updatedMessage += $this->defaultPayload;

                                $actionMapping = array_shift($objectMapping);
                                $attachments = $actionMapping['attachments'];
                                $self = $this;
                                $id = $object_it->getId();
                                $data = $this->model->get($class, $id, 'text', true);

                                array_walk_recursive(
                                    $attachments,
                                    function(&$item,$key) use($self, $data, $class, $id) {
                                        $item = $self->parseTemplate($item, $data, $class, $id);
                                    }
                                );
                                $attachments['fallback'] = $data['UID'];
                                $updatedMessage['attachments'] = json_encode(
                                    array(array_merge($updatedMessage['attachments'][0], $attachments[0]))
                                );

                                $response = $this->slack->execute("chat.postMessage", $updatedMessage);
                                $this->getLogger()->info("Update result ".var_export($response,true));
                            }
                        }
                    }
                }
                $this->timestamp = floatval($message['ts']) > floatval($this->timestamp)
                    ? $message['ts']
                    : $this->timestamp;
            }
        }

        return array(
            array(),
            ''
        );
    }

    public function readItem($mapping, $class, $id, $parms = array())
    {
        return array();
    }

    public function writeItem($mapping, $class, $id, $data, $queueItem)
    {
        $action = $queueItem['action'];
        $data['Changed'] = join(',',$queueItem['attributes']);

        if ( $this->usersMapping[$queueItem['author']] != '' ) {
            $authorChannel = $this->usersMapping[$queueItem['author']];
        }
        else {
            $authorChannel = $queueItem['author'];
        }

        if ( $mapping[$action] == '' ) {
            $this->getLogger()->info("Has no mapping for action: ".$queueItem['action']);
            return array();
        }

        $payload = $mapping[$action];

        if ( $payload['filter'] != '' ) {
            if ( ! $this->applyFilter($data, $payload['filter']) ) {
                $this->getLogger()->info("Object has been filtered by ".$payload['filter']);
                return array();
            }
            unset($payload['filter']);
        }

        $payload += $this->defaultPayload;
        $self = $this;

        array_walk_recursive(
            $payload,
            function(&$item,$key) use($self, $data, $class, $id) {
                $item = $self->parseTemplate($item, $data, $class, $id);
            }
        );
        $payload['attachments']['fallback'] = $data['UID'];
        $payload['attachments'] = json_encode(array_values($payload['attachments']));

        if ( trim($payload['channel']) == '' ) {
            $payload['channel'] = $this->channel;
        }

        $this->getLogger()->info("Slack command payload: ".var_export($payload, true));

        $channels = preg_split('/[\s,]+/', $payload['channel']);
        foreach( $channels as $channel ) {
            if ( $channel == $authorChannel ) continue; // skip direct notification to the author of changes
            $payload['channel'] = $channel;
            $response = $this->slack->execute("chat.postMessage", $payload);
            $this->getLogger()->info('Slack command result: '.var_export($response, true));
        }

        return array();
    }

    public function deleteItem($mapping, $class, $id)
    {
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
    }

    function buildDictionaries()
    {
        $response = $this->slack->execute("users.list", array());
        $responseBody = $response->getBody();
        $this->getLogger()->info("Users found ".count($responseBody['members']));

        foreach( $responseBody['members'] as $user ) {
            $this->usersMapping[$user['profile']['email']] = $user['id'];
            $this->users[$user['id']] = $user['name'];
            $this->getLogger()->info("Channel @".$user['id']." for ".$user['profile']['email']);
        }
    }

    protected function parseTemplate( $template, $data, $class, $id )
    {
        return preg_replace_callback('/%([\w0-9\._]+)/',
            function($matches) use ($data,$class,$id) {
                switch(strtolower($matches[1])) {
                    default:
                        $attribute_path = preg_split('/\./',$matches[1]);
                        $value = $data[array_shift($attribute_path)];
                        foreach( $attribute_path as $field ) {
                            if ( is_numeric(array_shift(array_keys($value))) ) {
                                $values = array();
                                foreach( $value as $itemValue ) {
                                    $values[] = $itemValue[$field];
                                }
                                $value = join(',',$values);
                            }
                            else {
                                $value = $value[$field];
                            }
                        }
                        if ( filter_var($value, FILTER_VALIDATE_EMAIL) ) {
                            return $this->usersMapping[$value];
                        }
                        else {
                            $text = new \Html2Text\Html2Text($value);
                            return $text->getText();
                        }
                }
            },
            $template
        );
    }

    protected function applyFilter( $data, $xpath )
    {
        $ids = array();
        $xml = '<?xml version="1.0" encoding="utf-8"?><Collection><Object id="">'.$this->arrayToXml($data).'</Object></Collection>';
        try {
            $xml_object = new \SimpleXMLElement($xml);
            foreach( $xml_object->xpath('/Collection/Object['.$xpath.']') as $item ) {
                foreach( $item->attributes() as $attribute => $value ) {
                    if ( $attribute == 'id' ) $ids[] = (string) $value;
                }
            }
        }
        catch( \Exception $ex ) {
            $this->getLogger()->error('queryXPath: '.$ex->getMessage());
            $this->getLogger()->error('XML body: '.$xml);
        }
        return count($ids) > 0;
    }

    protected function arrayToXml( $data ) {
        $xml = "";
        foreach( $data as $attribute => $value )
        {
            if ( is_array($value) ) {
                if ( is_numeric(array_shift(array_keys($value))) ) {
                    foreach( $value as $itemValue ) {
                        $xml .= '<'.$attribute.'>'.$this->arrayToXml($itemValue).'</'.$attribute.'>';
                    }
                }
                else {
                    $xml .= '<'.$attribute.'>'.$this->arrayToXml($value).'</'.$attribute.'>';
                }
            }
            else {
                $xml .= '<'.$attribute.'><![CDATA['.implode(explode(']]>', $value), ']]]]><![CDATA[>').']]></'.$attribute.'>';
            }
        }
        return $xml;
    }

    private $defaultPayload = array();
    private $users = array();
    private $usersMapping = array();
    private $slack = null;
    private $timestamp = '';
    private $uidService = null;
}