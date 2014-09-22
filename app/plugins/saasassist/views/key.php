<?php

define ('SAASSALT', 'b49ca47b46v46c581u3c34dlc0ac85d2');

date_default_timezone_set('UTC');

$key_value = md5(INSTALLATION_UID.'14'.SAASSALT.date('#2fee3ffY#3fe2a32m-@3@j', strtotime('-0 day', strtotime(date('Y-m-j')))));

