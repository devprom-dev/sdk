<?php

include "SmtpEncryptionModeRegistry.php";

class SmtpEncryptionMode extends MetaobjectCacheable
{
    function __construct()
    {
        parent::__construct('Priority', new SmtpEncryptionModeRegistry($this));
    }
    
    function getDisplayName()
    {
        return translate('Шифрование');
    }
}