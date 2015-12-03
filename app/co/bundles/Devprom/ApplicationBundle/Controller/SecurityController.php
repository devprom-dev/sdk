<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Devprom\ApplicationBundle\Service\LoginUserService;
use Devprom\CommonBundle\Service\Project\InviteService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if ( !class_exists('CoPage', false) ) include SERVER_ROOT_PATH."co/views/Common.php";
include_once SERVER_ROOT_PATH."core/classes/user/validators/ModelValidatorPasswordLength.php";

include SERVER_ROOT_PATH."co/views/LoginPage.php";
include SERVER_ROOT_PATH."co/views/RestorePage.php";
include SERVER_ROOT_PATH."co/views/PasswordPage.php";

class SecurityController extends PageController
{
    public function loginAction(Request $request)
    {
        $response = $this->checkDeploymentState($request);
        if ( is_object($response) ) return $response;

    	$user_it = getSession()->getUserIt();
        if ( $user_it->getId() > 0 )
        {
            return new RedirectResponse( 
            		$request->query->get('redirect') == '' ? $_SERVER['ENTRY_URL'] : $request->query->get('redirect') 
            );
        }
        else 
        {
        	$user_it = getFactory()->getObject('User')->getRegistry()->getAll();
        	if ( $user_it->count() < 1 )
        	{
        		return new RedirectResponse('/install');
        	}
        }
    	
    	return $this->responsePage( new \LoginPage() );
    }

    public function loginProcessAction(Request $request)
    {
        $session = getSession();

        if ( $session->getUserIt()->getId() == '' )
        {
            $auth_factory = $session->getAuthenticationFactory();
            if ( $auth_factory->credentialsRequired() )
            {
                $command = new LoginUserService();
                $result = $command->validate(
                    trim($request->request->get('login')),
                    trim($request->request->get('pass'))
                );

                if( $result > 0 ) {
                    $log = $this->getLogger();
                    if ( is_object($log) ) {
                        $log->info( 'Login used: '.$request->request->get('login') );
                        $log->info( 'Password hash: '.getFactory()->getObject('User')->getHashedPassword(trim($request->request->get('pass'))) );
                    }
                    return $this->replyError( $command->getResultDescription( $result ) );
                }
                $session->open( $command->getUserIt() );
            }
            else {
                $command = new LoginUserService();
                return $this->replyRedirectError('/logoff', $command->getResultDescription(2));
            }
        }

        if ( getSession()->getUserIt()->get('AskChangePassword') == 'Y' ) {
            return $this->replyRedirect(
                '/reset?key='.getSession()->getUserIt()->getResetPasswordKey()
            );
        } else {
            return $this->replyRedirect(
                $request->request->get('redirect') == '' ? $_SERVER['ENTRY_URL'] : $request->request->get('redirect')
            );
        }
    }

    public function loginCheckAction(Request $request)
    {
        if ( getSession()->getUserIt()->getId() > 0 ) {
            return $this->replyRedirect(
                $request->request->get('redirect') == '' ? $_SERVER['ENTRY_URL'] : $request->request->get('redirect')
            );
        } else {
            $command = new LoginUserService();
            return $this->replyRedirectError('/logoff', $command->getResultDescription(2));
        }
    }

    # region Restore Password
    
    public function restoreAction(Request $request)
    {
        $response = $this->checkDeploymentState($request);
        
        if ( is_object($response) ) return $response;
            	
    	return $this->responsePage( new \ForgetPasswordPage() );
    }
    
    public function restoreProcessAction(Request $request)
    {
        if ( $request->request->get('email') == '' ) return $this->replyError(text(219));

		$part_cls = getFactory()->getObject('cms_User');
		$part_it = $part_cls->getByRef('LCASE(Email)', strtolower(trim($request->request->get('email'))));

		if ( $part_it->getId() < 1) return $this->replyError(text(220));
        if ( $part_it->get('Password') == '' ) return $this->replyError(text(2061));

		// send email notification with the url to reset password
 		$settings_it = getFactory()->getObject('cms_SystemSettings')->getAll();
		
		$body = str_replace( '%1', \EnvironmentSettings::getServerUrl().'/reset?key='.$part_it->getResetPasswordKey(), text(221));
		
   		$mail = new \HtmlMailbox;
   		$mail->appendAddress($part_it->get('Email'));
   		$mail->setBody($body);
   		$mail->setSubject( text(222) );
   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
		$mail->send();
		
		return $this->replySuccess(text(223));
    }
    
    # region Reset Password
     
    function resetAction(Request $request)
    {
        $response = $this->checkDeploymentState($request);
        if ( is_object($response) ) return $response;

        $auth_factory = getSession()->getAuthenticationFactory();
        if ( is_object($auth_factory) && !$auth_factory->credentialsRequired() ) return new RedirectResponse('/');
    	
    	return $this->responsePage( new \ResetPasswordPage() );
        
    }
    
    function resetProcessAction(Request $request)
    {
		$response = $this->checkRequired( $request, array( 'NewPassword', 'RepeatPassword' ) );
		
		if ( is_object($response) ) return $response;
		if( $request->query->get('key') == '' ) return $this->replyError( text(231) );
		if( $request->request->get('NewPassword') != $request->request->get('RepeatPassword') ) return $this->replyError( text(232) );

    	$user = getFactory()->getObject('cms_User');
        $parms['Password'] = $request->request->get('NewPassword');

        $validators = new \ModelValidator();
        $validators->addValidator( new \ModelValidatorPasswordLength() );
        $message = $validators->validate( $user, $parms );
        if ( $message != "" ) {
            return $this->replyError($message);
        }

		$user->setNotificationEnabled(false);
    	
    	$user_it = $user->getAll();
		while ( !$user_it->end() )
		{
			if( trim($request->query->get('key')) == $user_it->getResetPasswordKey() ) 
			{
				$user->modify_parms($user_it->getId(),
						array(
						    'Password' => \IteratorBase::utf8towin($request->request->get('NewPassword')),
                            'AskChangePassword' => 'N'
						)
				);
					
				$session = getSession();
				
				$session->open( $user_it );
				
				if ( $request->request->get('page') != '' )
				{
				     return $this->replyRedirect( $request->request->get('page'), text(629) );
				}
				else
				{
				    return $this->replySuccess( text(629) );
				}
			}

			$user_it->moveNext();
		}

		return $this->replyError( text(233) );
    }
    
    public function logoffAction()
    {
        getSession()->close();
        
        return new RedirectResponse('/');
    }
    
    public function joinAction(Request $request)
    {
    	$email = $request->get('email');
    	if ( $email == '' ) throw new NotFoundHttpException('Email is required');

    	$service = new InviteService($this, getSession());
    	
    	$participant_it = $service->applyInvitation($email);
    	
    	if ( $participant_it->getId() < 1 ) throw new NotFoundHttpException('Unable process the invitation');
    	
    	return new RedirectResponse(
    			\EnvironmentSettings::getServerUrl().
    					'/reset?key='.$participant_it->getRef('SystemUser')->getResetPasswordKey().
    						'&redirect='.urlencode('/profile?redirect=/pm/'.$participant_it->getRef('Project')->get('CodeName'))
		);
    }
}