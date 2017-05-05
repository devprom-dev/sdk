<?php

include ('../common.php');
include ('nusoap/lib/nusoap.php');
include ('classes/SoapService.php');
include ('classes/SOAPSession.php');

$model_factory = new ModelFactoryProject(
    PluginsFactory::Instance(),
    getCacheService(),
    'soap',
    new APIAccessPolicy(getCacheService())
);

// create session object
$session = new SOAPSession();

// create main soap service class
$soap = new SoapService;

$delimiter = '.';
if ( in_array($_REQUEST['delim'], array('.','_')) ) {
    $delimiter = $_REQUEST['delim'];
}
else {
    $headers = apache_request_headers();
    if ( in_array($headers['Methods-Delimiter'], array('.','_')) ) {
        $delimiter = $headers['Methods-Delimiter'];
    }

    $rawData = file_get_contents("php://input");
    if ( stripos($rawData, '_remote') !== false ) {
        $delimiter = '_';
    }
}
if ( $delimiter == '_' ) {
    $_REQUEST['style'] = 'document';
    $_REQUEST['use'] = 'literal';
}
$soap->setMethodDelimiter($delimiter);
