<?php

class UsersAndInvitationsRegistry extends ObjectRegistrySQL
{
	public function setProject( $project )
	{
		$this->project = $project;
	}
	
	public function getFilters()
	{
		return array_merge(
				array (
						new UserStatePredicate('nonblocked')
				),
				parent::getFilters()
		);
	}
	
  	function getQueryClause()
 	{
 	    return " ( SELECT t.cms_UserId, t.Caption, t.Email, t.Phone, t.RecordModified, t.RecordCreated, 0 Invitation, t.IsReadonly ".
 	           "	 FROM cms_User t ".
			   "    UNION ALL ".
			   "   SELECT 0, t.Addressee, t.Addressee, '', t.RecordModified, t.RecordCreated, t.pm_InvitationId, 'N' ".
			   "	 FROM pm_Invitation t ".
			   "	 WHERE t.Project = ".$this->project." ) ";
 	}
 	
 	private $project = 0;
}