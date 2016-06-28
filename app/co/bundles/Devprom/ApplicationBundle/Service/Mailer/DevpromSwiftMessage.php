<?php
namespace Devprom\ApplicationBundle\Service\Mailer;

use Swift_Message;
use Swift_InputByteStream;

class DevpromSwiftMessage extends Swift_Message
{
    private $message = '';

    public function setBodyNative( $text ) {
        $this->message = $text;
        return $this;
    }

    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null) {
        return new self($subject, $body, $contentType, $charset);
    }

    public function toByteStream(Swift_InputByteStream $is)
    {
        $is->write($this->getHeaders()->toString());
        $is->commit();

        $is->write($this->message);
    }
}