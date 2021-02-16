<?php
include('common.php');

include_once SERVER_ROOT_PATH.'core/methods/AutocompleteWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/ProcessEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/DeleteEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/ExcelExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/BoardExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/HtmlExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/PrintPDFExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/XmlExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/FilterFreezeWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/BulkDeleteWebMethod.php';
include_once SERVER_ROOT_PATH."core/methods/DeleteObjectWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ModifyAttributeWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/AutoSaveFieldWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterObjectMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterTextWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/SettingsWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/CloneWikiPageWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/OpenWorkItemWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/BindIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/MergeIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SetTagsTaskWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SetWatchersWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';
include_once SERVER_ROOT_PATH.'pm/methods/FunctionFilterStateWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/methods/RevertWikiWebMethod.php';
include_once SERVER_ROOT_PATH."pm/methods/DocNewChildWebMethod.php";
include_once('methods/TransitionStateMethod.php');
include_once('methods/MoveToProjectWebMethod.php');
include_once('methods/WatchWebMethod.php');
include_once('methods/ResetBurndownWebMethod.php');
include_once('methods/WikiFilterActualLinkWebMethod.php');
include_once('methods/ReportModifyWebMethod.php');
include_once('methods/ViewCustomDictionaryWebMethod.php');
include_once('methods/CommentDeleteWebMethod.php');
include_once('methods/CommentDeleteNextWebMethod.php');
include_once "methods/WikiRemoveStyleWebMethod.php";
include_once "methods/ActuateWikiLinkWebMethod.php";
include_once "methods/CompareDocumentsWebMethod.php";
include_once "methods/OpenBrokenTraceWebMethod.php";
include_once "methods/IgnoreWikiLinkWebMethod.php";
include_once "methods/GotoReportWebMethod.php";
include_once "methods/StateExFilterWebMethod.php";
include_once "methods/FilterStateTransitionMethod.php";
include_once "methods/FilterStateMethod.php";
include_once "methods/UndoWebMethod.php";
include_once "methods/SetTagsRequestWebMethod.php";
include_once "methods/SetTagsWikiWebMethod.php";
include_once "methods/WikiExportBaseWebMethod.php";
include_once "methods/TaskConvertToIssueWebMethod.php";
include_once "methods/MarkChangesAsReadWebMethod.php";
include_once "methods/CreateIssueBasedOnWebMethod.php";
include_once "methods/ReintegrateWikiTraceWebMethod.php";
include_once "methods/ModifyStateWebMethod.php";

if ( !class_exists($_REQUEST['method'], false) ) throw new Exception('There is no such method');

getSession()->addBuilder( new WorkflowModelBuilder() );

$method = new $_REQUEST['method'];
if ( !is_a($method, 'WebMethod') ) throw new Exception('Unknown method class: '.$_REQUEST['method']);

try
{
    \FeatureTouch::Instance()->touch(strtolower(get_class($method)));
    getFactory()->getEventsManager()->delayNotifications();

    getSession()->addBuilder( new RequestModelExtendedBuilder() );

    $method->exportHeaders();
    $method->execute_request();

    getFactory()->getEventsManager()->releaseNotifications();
}
catch( Exception $e )
{
    $logger = \Logger::getLogger('System');
    if ( is_object($logger) ) $logger->error($e->getMessage());
}
