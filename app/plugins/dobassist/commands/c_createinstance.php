<?php

include_once SERVER_ROOT_PATH."cms/c_mail.php";

class CreateInstance extends CommandForm
{
	private $instance = '';
	private $email = '';
	private $username = '';
	private $userlogin = '';
	private $template = '';
	private $language = 'en';
	private $names_restricted = array('docs','doc','api','support','www','account','news','blog');
	
 	function validate()
 	{
 		header('Access-Control-Allow-Origin: *');
 		header('Access-Control-Allow-Methods: *');
 		header('Access-Control-Allow-Headers: *');
 		
 		$this->checkRequired( array('instance', 'email', 'username') );

 		$this->instance = trim(strtolower($_REQUEST['instance']));
 		$this->template = trim(strtolower($_REQUEST['template']));
		if ( in_array($_REQUEST['language'], array('ru','en')) ) {
			$this->language = trim(strtolower($_REQUEST['language']));
		}

 	 	if ( filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) === false ) $this->replyError(text('dobassist2'));
 		if ( !preg_match('/^[A-Za-z0-9]+$/', $this->instance, $matches) ) $this->replyError(text('dobassist3'));
 		if ( is_dir($this->getLogsDir()) || in_array($this->instance, $this->names_restricted) ) $this->replyError(text('dobassist5'));
 	 	$length = strlen($this->instance);
 		if ( $length < 3 || $length > 25 ) $this->replyError(text('dobassist4'));
 		if ( $this->template != '' && !preg_match('/^[A-Za-z0-9\-\_\.]+$/', $this->template, $matches) ) $this->replyError(text('dobassist26'));
 		
 		$this->email = $_REQUEST['email'];
 		list($this->userlogin, $server) = preg_split('/\@/', $this->email);
 		$this->username = htmlentities(IteratorBase::utf8towin($_REQUEST['username']), ENT_COMPAT | ENT_HTML401, 'utf-8');
 		
		return true;
 	}
 	
 	function create()
	{
		$this->sendMail( shell_exec(SAAS_ROOT.'pop-instance.sh '.$this->instance.' 2>&1') );
		
		$this->replyRedirect(
				SAAS_SCHEME.'://'.$this->instance.'.'.SAAS_DOMAIN.'/module/dobassist/initialize?'.
					'template='.$this->template.'&username='.$this->username.'&userlogin='.$this->userlogin.'&useremail='.$this->email.'&l='.$this->language,
				text('dobassist34')
 		);
	}
	
	protected function getLogsDir()
	{
		return SAAS_ROOT.'users/'.$this->instance.'/';
	}
	
	protected function sendMail( $log )
	{
	    $mail = new HtmlMailbox;
	    $mail->appendAddress('info@devprom.ru');
	    
	    $body = 'DevOps Board instance is created<br/>';
	    $body .= $this->instance.'.'.SAAS_DOMAIN.'<br/>';
	    
		$body .= $log.'<br/>';
		$body .= nl2br(file_get_contents($this->getLogsDir().'install.log')).'<br/>';
		$body .= nl2br(file_get_contents($this->getLogsDir().'complete.log')).'<br/>';
		
	    $mail->setBody($body);
	    $mail->setSubject( 'DevOps Board instance has been created' );
	    $mail->setFrom(SAAS_SENDER);
	    $mail->send();
	}
}
