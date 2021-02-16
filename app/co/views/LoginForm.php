<?php
use \Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginForm extends AjaxForm
{
    private $session = null;

    function __construct( $object, SessionInterface $session ) {
        $this->session = $session;
        parent::__construct($object);
    }

    function getWidth()
	{
		return '40%';
	}
	
	function getTemplate()
	{
		return "co/FormAsyncLogin.php";
	}
	
	function getActions()
	{
	    return array (
            array ( 'name' => text(1330), 'url' => "/recovery" )
        );
	}

	function getRenderParms($view)
    {
        return array_merge(
            parent::getRenderParms($view),
            array (
                'redirect_url' => SanitizeUrl::parseUrl($this->session->get('redirect')),
                'login_url' => '/auth'
            )
        );
    }
}