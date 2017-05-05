<?php

class IssueAutoAction extends AutoAction
{
 	function getSubjectClassName()
 	{
 		return 'request';
 	}
 	
 	function getActionAttributes()
 	{
 		return array_merge(
 		    array (
                'State'
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
                'Release'
            )
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
					'SupportChannelName'
 				)
 		);
 	}
 	
 	function getPage()
 	{
 		return getSession()->getApplicationUrl($this).'autoactions?';
 	}
}
