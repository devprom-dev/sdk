<?php

include_once SERVER_ROOT_PATH."cms/c_mail.php";

class CreateInstance extends CommandForm
{
	private $instance = '';
	private $email = '';
	private $username = '';
	private $userlogin = '';
	private $template = '';
	
 	function validate()
 	{
 		$this->checkRequired( array('instance', 'email', 'username') );
 		
 	 	if ( filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) === false ) $this->replyError(text('dobassist2'));
 		if ( !preg_match('/^[A-Za-z0-9]+$/', $_REQUEST['instance'], $matches) ) $this->replyError(text('dobassist3'));
 	 	if ( $_REQUEST['template'] != '' && !preg_match('/^[A-Za-z0-9\-\_\.]+$/', $_REQUEST['template'], $matches) )
 		{
 			$this->replyError(text('dobassist26'));
 		}
 		
 		$this->template = $_REQUEST['template'];
 		
 		$length = strlen($_REQUEST['instance']);
 		if ( $length < 3 || $length > 25 ) $this->replyError(text('dobassist4'));
 		
 		$this->instance = strtolower($_REQUEST['instance']);

 		if ( is_dir($this->getLogsDir()) ) $this->replyError(text('dobassist5'));
 		
 		$this->email = $_REQUEST['email'];
 		list($this->userlogin, $server) = preg_split('/\@/', $this->email);
 		$this->username = htmlentities(IteratorBase::utf8towin($_REQUEST['username']), ENT_COMPAT | ENT_HTML401, 'windows-1251');
 		
		return true;
 	}
 	
 	function create()
	{
		$this->sendMail( shell_exec(SAAS_ROOT.'pop-instance.sh '.$this->instance.' 2>&1') );
		
		$this->replyRedirect(
				SAAS_SCHEME.'://'.$this->instance.'.'.SAAS_DOMAIN.'/module/dobassist/initialize?'.
					'template='.$this->template.'&username='.$this->username.'&userlogin='.$this->userlogin.'&useremail='.$this->email,
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
	    $mail->appendAddress('admin@devopsboard.com');
	    
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
