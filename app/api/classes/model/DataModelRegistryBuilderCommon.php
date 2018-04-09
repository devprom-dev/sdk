<?php

include_once SERVER_ROOT_PATH."api/classes/model/IDataModelRegistryBuilder.php";

class DataModelRegistryBuilderCommon implements IDataModelRegistryBuilder
{
	public function build( DataModelRegistry & $registry )
	{ 
		$registry->addClass( array (
            'Activity',
            'Attachment',
            'Blog',
            'BlogPost',
            'BlogPostFile',
            'ChangeLog',
            'Comment',
            'Environment',
            'Iteration',
            'Methodology',
            'Feature',
            'Milestone',
            'Participant',
            'ParticipantRole',
            'Priority',
            'Project',
            'ProjectPage',
            'ProjectRole',
            'ProjectRoleBase',
            'Question',
            'Release',
            'Request',
            'RequestLink',
            'RequestType',
            'Task',
            'TaskType',
            'User',
            'WikiPage',
            'WikiPageFile',
            'Snapshot',
            'StateBase',
            'Watcher',
            'Customer',
            'PMCustomAttribute',
            'PMCustomAttributeValue'
		));
	}
}