<?php

class FieldRestUISettings extends Field
{
	private $url = '';
	
	function __construct( $url )
	{
		$this->url = $url;
		parent::__construct();
	}
	
    function render( $view )
    {
        echo $view->render('pm/FieldResetUISettings.php', array (
			'url' => $this->url,
			'template' => getSession()->getProjectIt()->get('Tools')
        ));
    }
}