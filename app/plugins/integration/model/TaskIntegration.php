<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/Task.php";
include_once "TaskIntegrationRegistry.php";

class TaskIntegration extends Task
{
	public function __construct()
	{
		parent::__construct(new TaskIntegrationRegistry($this));
        $this->addAttribute('ExternalLink', 'VARCHAR', text('integration8'), true);
	}
}