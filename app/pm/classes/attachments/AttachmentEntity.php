<?php
include "AttachmentEntityRegistry.php";

class AttachmentEntity extends Metaobject
{
 	function __construct() {
 		parent::Metaobject('entity', new AttachmentEntityRegistry($this));
 	}

    function getVpds() {
        return array();
    }
}