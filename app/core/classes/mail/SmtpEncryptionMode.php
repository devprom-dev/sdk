<?php
include "SmtpEncryptionModeRegistry.php";

class SmtpEncryptionMode extends MetaobjectCacheable
{
    function __construct() {
        parent::__construct('entity', new SmtpEncryptionModeRegistry($this));
    }
    
    function getDisplayName() {
        return translate('Шифрование');
    }

    function IsDictionary() {
        return true;
    }
}