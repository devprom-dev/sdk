<?php

use Devprom\ApplicationBundle\Service\CreateProjectService;

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
 		
 	 	if ( filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) === false )
 		{
 			$this->replyError(text('saasassist2'));
 		}
 		
 		if ( !preg_match('/^[A-Za-z0-9]+$/', $_REQUEST['instance'], $matches) )
 		{
 			$this->replyError(text('saasassist3'));
 		}
 		
 	 	if ( $_REQUEST['template'] != '' && !preg_match('/^[A-Za-z0-9\-]+$/', $_REQUEST['template'], $matches) )
 		{
 			$this->replyError(text('saasassist26'));
 		}
 		
 		$this->template = $_REQUEST['template'];
 		
 		$length = strlen($_REQUEST['instance']);
 		
 		if ( $length < 3 || $length > 25 )
 		{
 			$this->replyError(text('saasassist4'));
 		}
 		
 		$this->instance = strtolower($_REQUEST['instance']);
 		
 		if ( is_dir($this->getLogsDir()) )
 		{
 			$this->replyError(text('saasassist5'));
 		}
 		
 		$this->email = $_REQUEST['email'];
 	
 		list($this->userlogin, $server) = preg_split('/\@/', $this->email);

 		$this->username = htmlentities(IteratorBase::utf8towin($_REQUEST['username']), ENT_COMPAT | ENT_HTML401, 'windows-1251');
 		
		return true;
 	}
 	
 	function create()
	{
		$this->sendMail( shell_exec('/home/saas/clone.sh '.$this->instance.' 2>&1') );
		
		$this->replyRedirect(
				'https://'.$this->instance.'.'.SAAS_DOMAIN.'/module/saasassist/initialize?'.
					'template='.$this->template.'&username='.$this->username.'&userlogin='.$this->userlogin.'&useremail='.$this->email,
				text('saasassist34')
 		);
	}
	
	protected function getLogsDir()
	{
		return '/home/saas/users/'.$this->instance.'/';
	}
	
	protected function sendMail( $log )
	{
	    $mail = new HtmlMailbox;
	    $mail->appendAddress('marketing@devprom.ru');
	    
	    $body = 'Пользователь создал экземпляр Devprom.ALM<br/>';
	    $body .= $this->instance.'.'.SAAS_DOMAIN.'<br/>';
	    
		$body .= $log.'<br/>';
		$body .= nl2br(file_get_contents($this->getLogsDir().'install.log')).'<br/>';
		$body .= nl2br(file_get_contents($this->getLogsDir().'complete.log')).'<br/>';
		
	    $mail->setBody($body);
	    
	    $mail->setSubject( 'SaaS: новый экземпляр Devprom.ALM' );
	    $mail->setFrom(SAAS_SENDER);
	    	
	    $mail->send();
	}
}
