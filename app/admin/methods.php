<?php

include dirname(__FILE__).'/../app/bootstrap.php';

include_once SERVER_ROOT_PATH.'core/methods/ProcessEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/DeleteEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/ExcelExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/BoardExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/HtmlExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/FilterFreezeWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/BulkDeleteWebMethod.php';
include_once SERVER_ROOT_PATH."core/methods/DeleteObjectWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ModifyAttributeWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/TranslateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/AutoSaveFieldWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ExecuteAutoCompleteWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterObjectMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterTextWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/GetAttributeWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";

include_once SERVER_ROOT_PATH.'core/c_more.php';

include_once('methods/c_user_methods.php');
include_once('methods/c_plugin_methods.php');
include_once('methods/c_project_methods.php');
include_once('methods/c_check_methods.php');

if ( !class_exists($_REQUEST['method'], false) ) throw new Exception('There is no such method');

$method = new $_REQUEST['method'];
 
if ( !is_a($method, 'WebMethod') ) throw new Exception('Unknown method class: '.SanitizeUrl::parseUrl($_REQUEST['method']));

\FeatureTouch::Instance()->touch(strtolower(get_class($method)));
$method->exportHeaders();
$method->execute_request();
