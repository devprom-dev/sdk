<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/MetaobjectStatable.php";
include_once SERVER_ROOT_PATH.'pm/classes/tags/Tag.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/PMWikiPage.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageTemplate.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageFile.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageChange.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tags/WikiTag.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/PMBlogPost.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/BlogPostDates.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/BlogPostFile.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/Blog.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskType.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskTypeBase.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskTypeUnified.php';
include_once SERVER_ROOT_PATH.'pm/classes/plan/Iteration.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/Task.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/WorkItem.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/WorkItemType.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/Request.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestAsTarget.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTypeUnified.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/IssueAuthor.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/IssueActualAuthor.php';
include_once SERVER_ROOT_PATH.'pm/classes/project/ProjectMetric.php';
include_once SERVER_ROOT_PATH.'pm/classes/project/ProjectAccessible.php';
include_once SERVER_ROOT_PATH.'pm/classes/project/ProjectExceptCurrent.php';
include_once SERVER_ROOT_PATH.'pm/classes/communications/KnowledgeBaseTemplate.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/ProjectPage.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/participants/Participant.php';
include_once SERVER_ROOT_PATH.'pm/classes/participants/ParticipantRole.php';
include_once SERVER_ROOT_PATH.'pm/classes/participants/ParticipantProjectRelated.php';
include_once SERVER_ROOT_PATH.'pm/classes/product/Feature.php';
include_once SERVER_ROOT_PATH.'pm/classes/product/FeatureTerminal.php';
include_once SERVER_ROOT_PATH.'pm/classes/product/FeatureType.php';
include_once SERVER_ROOT_PATH.'pm/classes/settings/Methodology.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/attachments/Attachment.php';
include_once SERVER_ROOT_PATH.'pm/classes/attachments/AttachmentUnified.php';
include_once SERVER_ROOT_PATH.'pm/classes/attachments/AttachmentEntity.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestAttachment.php';
include_once SERVER_ROOT_PATH.'pm/classes/time/Activity.php';
include_once SERVER_ROOT_PATH.'pm/classes/time/ActivityRequest.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/time/ActivityTask.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tags/RequestTag.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/Question.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/c_download.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/plan/Milestone.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/participants/ProjectRole.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/participants/ProjectRoleBase.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/participants/ParticipantTester.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestLink.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestLinkType.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/permissions/AccessRight.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/plan/Version.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/plan/Stage.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/plan/Release.php';
include_once SERVER_ROOT_PATH.'pm/classes/plan/ReleaseActual.php';
include_once SERVER_ROOT_PATH.'pm/classes/plan/ReleaseRecent.php';
include_once SERVER_ROOT_PATH.'pm/classes/watchers/Watcher.php';
include_once SERVER_ROOT_PATH.'pm/classes/watchers/WatcherUser.php';
include_once SERVER_ROOT_PATH.'pm/classes/permissions/AccessObject.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/permissions/CommonAccessRight.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskTypeStage.php';
include_once SERVER_ROOT_PATH.'pm/classes/report/Report.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/report/PMReport.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/report/PMReportCategory.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/widgets/Widget.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/ObjectsListWidget.php';
include_once SERVER_ROOT_PATH.'pm/classes/time/SpentTime.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestType.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTraceBase.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTraceQuestion.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestInversedTraceQuestion.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTraceMilestone.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestInversedTraceMilestone.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskTraceBase.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskTraceTask.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskInversedTraceTask.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/PrecedingTask.php';
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskTypeState.php';
include_once SERVER_ROOT_PATH.'pm/classes/tags/CustomTag.php';
include_once SERVER_ROOT_PATH.'pm/classes/tags/FeatureTag.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tags/QuestionTag.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/product/Importance.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/StateBase.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/StateMeta.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/QuestionState.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tasks/TaskState.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/IssueState.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/Transition.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/TransitionRole.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/TransitionPredicate.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/StateBusinessRule.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/StateBusinessAction.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/settings/Dictionary.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/settings/Workflow.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/TransitionAttribute.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/StateAttribute.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/TransitionResetField.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageTrace.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageInversedTrace.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageTraceType.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/communications/Notification.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/report/PMCustomReport.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/c_entity_cluster.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/workflow/StateAction.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/tags/BlogPostTag.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiTypeBase.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/common/SearchableObjectSet.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/common/HistoricalObjects.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/common/CustomizableObjectSet.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/project/ProjectTemplateSections.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/common/SharedObjectSet.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/product/FunctionTrace.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/participants/ProjectUser.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/widgets/Workspace.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/widgets/WorkspaceMenu.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/widgets/WorkspaceMenuItem.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/permissions/AttributePermissionEntity.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTemplate.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiPageBranch.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/project/Baseline.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/ProjectStage.php'; 
include_once SERVER_ROOT_PATH.'pm/classes/comments/Comment.php';
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestComment.php';
include_once SERVER_ROOT_PATH.'pm/classes/resources/CustomResource.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalArea.php';
include_once SERVER_ROOT_PATH.'pm/classes/settings/CustomAttributeType.php';
include_once SERVER_ROOT_PATH.'pm/classes/communications/ChangeLogTemplate.php';
include_once SERVER_ROOT_PATH.'pm/classes/report/TextChangeHistory.php';
include_once SERVER_ROOT_PATH.'pm/classes/participants/Mentioned.php';
