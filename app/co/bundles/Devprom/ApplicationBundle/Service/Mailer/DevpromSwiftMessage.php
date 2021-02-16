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
        if ( $this->message == '' ) return parent::toByteStream($is);

        $headers = $this->getHeaders();
        $headers->addTextHeader('Auto-Submitted', 'auto-generated');
        $headers->addTextHeader('X-Auto-Response-Suppress', 'All');

        $is->write($headers->toString());
        $is->commit();

        $is->write($this->message);
    }
}