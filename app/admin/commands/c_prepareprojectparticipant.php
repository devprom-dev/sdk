<?php

////////////////////////////////////////////////////////////////////////////
class PrepareProjectParticipant extends CommandForm
{
	function validate()
	{
		global $_REQUEST, $model_factory;

		$this->checkRequired(
		array('SystemUser', 'Project') );

		return true;
	}

	function create()
	{
		global $_REQUEST, $model_factory;

		$project = $model_factory->getObject('pm_Project');
		$project_it = $project->getExact($_REQUEST['Project']);

		if ( $project_it->count() < 1 )
		{
			$this->replyError( text(200) );
		}

		$user = $model_factory->getObject('cms_User');
		$user_it = $user->getExact($_REQUEST['SystemUser']);

		$this->replyRedirect(
		$user_it->getViewUrl().'&mode=role&project='.$project_it->getId());
	}
}

?>