<?php

class FieldRestMySettings extends Field
{
	private $url = '';
	
	function __construct( $url )
	{
		$this->url = $url;
		parent::__construct();
	}
	
    function render( $view )
    {
 		$project_roles = getSession()->getRoles();
        echo $view->render('pm/FieldResetMySettings.php', array (
			'url' => $this->url,
			'lead_role' => $project_roles['lead']
        ));
    }
}