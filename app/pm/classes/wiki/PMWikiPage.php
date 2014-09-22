<?php

include_once "WikiPage.php";
include "PMWikiPageIterator.php";
include "predicates/PMWikiStageFilter.php";
include "predicates/PMWikiLinkedStateFilter.php";
include "predicates/PMWikiSourceFilter.php";
include "predicates/WikiRelatedIssuesPredicate.php";
include "predicates/WikiInArchivePredicate.php";
include "persisters/WikiPageFeaturePersister.php";
include "persisters/WikiPageDetailsPersister.php";
include "persisters/WikiTagsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";

class PMWikiPage extends WikiPage 
{
 	function __construct()
 	{
 		global $model_factory;
 		
		parent::__construct();
 		
		$this->setAttributeType('ParentPage', 'REF_'.get_class($this).'Id');
		
		$this->addAttribute('DocumentId', 'REF_'.get_class($this).'Id', translate('Документ'), false);
		
		$this->addAttribute('RecentComment', 'RICHTEXT', text(1198), false);
		
		$comment = $model_factory->getObject('Comment');
		
		$this->addPersister( new CommentRecentPersister() );
		
		$this->addPersister( new WikiPageDetailsPersister() );
		
		$this->addAttribute('Attachments', '', translate('Приложения'), false);
		
		$this->addAttribute( 'Tags', 'REF_WikiTagId', translate('Тэги'), false );
		
		$this->addPersister( new WikiTagsPersister() );
		
		$this->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), false);

		$watcher = $model_factory->getObject('pm_Watcher');

		$this->addPersister( new WatchersPersister() );

		$system_attributes = array( 
		        'UserField1',
		        'UserField2',
		        'UserField3',
		        'IsTemplate',
		        'IsDraft',
		        'ReferenceName',
		        'IsArchived',
		        'ContentEditor'
	    );
		        
		foreach( $system_attributes as $attribute )
		{
			$this->addAttributeGroup($attribute, 'system');
		}
 	}
 	
	function createIterator() 
	{
		return new PMWikiPageIterator($this);
	}
	
	function IsStatable()
	{
		global $model_factory;
		
		if ( $this->getStateClassName() == '' ) return false;
		
		$state = $model_factory->getObject($this->getStateClassName());
		
		return $state->getRecordCount() > 0;
	}
	
	function IsAttributeRequired( $name ) 
	{
		switch ( $name )
		{
			case 'Caption':
				return true;
				
			default:
				return parent::IsAttributeRequired( $name );
		}
	}
	
 	function getStateClassName()
 	{
		return '';
 	}

	//----------------------------------------------------------------------------------------------------------
	function getVersionsIt()
	{
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
		{
			$sql = 
				"SELECT tr.ObjectId WikiId, rl.Version, rl.pm_ReleaseId `Release` ".
			    " 		   FROM pm_ChangeRequestTrace tr, pm_Task ts, pm_Release rl " .
 			    "		  WHERE tr.ObjectClass = '".strtolower(get_class($this))."'" .
 			    "			AND ts.ChangeRequest = tr.ChangeRequest" .
 			    "			AND ts.Release = rl.pm_ReleaseId" .
 			    "		  UNION " .
 			    "		 SELECT tr.ObjectId WikiId, rl.Version, rl.pm_ReleaseId ".
			    " 		   FROM pm_TaskTrace tr, pm_Task ts, pm_Release rl " .
 			    "		  WHERE tr.ObjectClass = '".strtolower(get_class($this))."'" .
 			    "			AND ts.pm_TaskId = tr.Task" .
 			    "			AND ts.Release = rl.pm_ReleaseId ";
		}
		else
		{
			$sql = 
				"SELECT tr.ObjectId WikiId, req.PlannedRelease Version, NULL `Release` ".
		        " 		   FROM pm_ChangeRequestTrace tr, pm_ChangeRequest req " .
 			    "		  WHERE tr.ObjectClass = '".strtolower(get_class($this))."'" .
 			    "			AND tr.ChangeRequest = req.pm_ChangeRequestId ";
		}
		
		$sql = " SELECT t.WikiPageId, v.Version, v.Release ".
			   "   FROM ".$this->getRegistry()->getQueryClause()." t, ".
			   "        (".$sql.") v ".	
			   "  WHERE v.WikiId = t.WikiPageId ".
			   $this->getVpdPredicate().$this->getFilterPredicate().
		       "  ORDER BY t.WikiPageId ";
		
		return $this->createSQLIterator( $sql );
	}
	
	function getTypeIt()
	{
		return null;
	}
	
	function getPage()
	{
	}
	
	function getPageHistory()
	{
	}
	
	function getAttributeObject( $attr )
	{
		switch ( $attr )
		{
			case 'ParentPage':
				return $this;
				
			default:
				return parent::getAttributeObject( $attr );
		}
	}
}