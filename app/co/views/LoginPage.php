<?php
use \Symfony\Component\HttpFoundation\Session\SessionInterface;
include ('LoginForm.php');

class LoginPage extends CoPage
{
    private $session = null;

    function __construct( SessionInterface $session )
    {
        $this->session = $session;
        parent::__construct();
    }

 	function getTable()
 	{
 		return new LoginForm(getFactory()->getObject('cms_User'), $this->session);
 	}
}
