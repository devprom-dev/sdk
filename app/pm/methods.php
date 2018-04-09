<?php

include('common.php');

include_once SERVER_ROOT_PATH.'core/methods/ProcessEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/DeleteEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/ExcelExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/BoardExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/HtmlExportWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/XmlExportWebMethod.php';
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
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/GetAttributeWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/GetWholeTextWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/SettingsWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/CloneWikiPageWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/OpenWorkItemWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/BindIssuesWebMethod.php";

include_once SERVER_ROOT_PATH.'core/c_more.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';

include_once('methods/c_state_methods.php');
include_once('methods/c_priority_methods.php');
include_once('methods/c_project_methods.php');
include_once('methods/c_request_methods.php');
include_once('methods/c_task_methods.php');
include_once('methods/c_watcher_methods.php');
include_once('methods/c_date_methods.php');
include_once('methods/c_stage_methods.php');
include_once('methods/c_function_methods.php');
include_once('methods/c_user_methods.php');
include_once('methods/c_wiki_methods.php');
include_once('methods/c_report_methods.php');
include_once('methods/c_common_methods.php');
include_once('methods/CommentDeleteWebMethod.php');
include_once('methods/CommentDeleteNextWebMethod.php');
include_once('methods/ViewSpentTimeWebMethod.php');
include_once "methods/WikiRemoveStyleWebMethod.php";
include_once "methods/MakeSnapshotWebMethod.php";
include_once "methods/ActuateWikiLinkWebMethod.php";
include_once "methods/SyncWikiLinkWebMethod.php";
include_once "methods/CompareDocumentsWebMethod.php";
include_once "methods/OpenBrokenTraceWebMethod.php";
include_once "methods/IgnoreWikiLinkWebMethod.php";
include_once "methods/ReorderWebMethod.php";
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
include_once "methods/ReintegrateWikiPageWebMethod.php";
include_once "methods/ReintegrateWikiTraceWebMethod.php";

if ( !class_exists($_REQUEST['method'], false) ) throw new Exception('There is no such method');

getSession()->addBuilder( new WorkflowModelBuilder() );

$method = new $_REQUEST['method'];
if ( !is_a($method, 'WebMethod') ) throw new Exception('Unknown method class: '.$_REQUEST['method']);

try
{
    \FeatureTouch::Instance()->touch(strtolower(get_class($method)));

    $method->exportHeaders();
    $method->execute_request();
}
catch( Exception $e )
{
    $logger = \Logger::getLogger('System');
    if ( is_object($logger) ) $logger->error($e->getMessage());


}
