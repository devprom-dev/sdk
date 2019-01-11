<?php
include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";
include_once "RequestIntegrationRegistry.php";

class RequestIntegration extends Request
{
	public function __construct()
	{
		parent::__construct(new RequestIntegrationRegistry($this));
		$this->addAttribute('ExternalLink', 'VARCHAR', text('integration8'), true);
	}
}