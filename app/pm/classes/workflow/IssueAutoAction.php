<?php
include 'IssueAutoActionTaskModelBuilder.php';
include "IssueAutoActionMetadataBuilder.php";
include "IssueAutoActionModelBuilder.php";

class IssueAutoAction extends AutoAction
{
    function __construct()
    {
        parent::__construct();

        $builder = new IssueAutoActionModelBuilder();
        $builder->build($this);
        $builder = new IssueAutoActionTaskModelBuilder();
        $builder->build($this);
    }

    function getSubjectClassName()
 	{
 		return 'request';
 	}
 	
 	function getActionAttributes()
 	{
 		return array_merge(
 		    array (
                'State',
                'StateDuration'
            ),
            parent::getActionAttributes(),
            array(
                'Estimation',
                'Type',
                'Priority',
                'Environment',
                'Owner',
                'Function',
                'Severity',
                'Project',
                'Iteration',
                'PlannedRelease',
                'Fact',
                'FactToday'
            ),
            $this->getAttributesByGroup('task')
 		);
 	}
 	
 	function getConditionAttributes()
 	{
 		return array_merge(
 				parent::getConditionAttributes(),
 				array (
					'Description',
					'Author',
					'Company',
					'TimesRepeated',
					'SupportChannelEmail',
					'SupportChannelName',
                    'SubmittedVersion',
                    'ClosedInVersion',
                    'ResponseSLA',
                    'LeadTimeSLA'
 				)
 		);
 	}
 	
 	function getPage()
 	{
 		return getSession()->getApplicationUrl($this).'autoactions?';
 	}
}
