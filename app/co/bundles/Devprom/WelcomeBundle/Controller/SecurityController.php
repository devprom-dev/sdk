<?php

namespace Devprom\WelcomeBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Devprom\WelcomeBundle\Service\LoginUserService;
use Devprom\WelcomeBundle\Service\RestorePasswordService;
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

        $redirect = $request->query->get('redirect') == ''
            ? $request->server->get('HTTP_REFERER')
            : $request->query->get('redirect');

        $url_parts = parse_url($redirect);
        $path = trim($url_parts['path'],'/');
        if ( in_array($path, array('login','logoff','openid')) ) {
            $redirect = '';
        }
        elseif( $path == 'auth' ) {
            $command = new LoginUserService();
            return $this->replyError( $command->getResultDescription(LoginUserService::WRONG_PASSWORD) );
        }
        else {
            $query_parts = array();
            parse_str($request->server->get('QUERY_STRING'), $query_parts);
            unset($query_parts['redirect']);
            if ( count($query_parts) > 0 ) {
                $redirect .= '&' . http_build_query($query_parts);
            }
            $redirect = \SanitizeUrl::parseUrl($redirect);

            if ( defined('AUTH_OPENID_ONLY') ) {
                return $this->openidAction($request);
            }
        }

        if ( getSession()->getUserIt()->getId() > 0 ) {
            return new RedirectResponse($_SERVER['ENTRY_URL']);
        }

        $user_it = getFactory()->getObject('User')->getRegistry()->Query(array());
        if ( $user_it->count() < 1 ) {
            return new RedirectResponse('/install');
        }

        $request->getSession()->set('redirect', $redirect);
    	return $this->responsePage( new \LoginPage($request->getSession()) );
    }

    // used for native authentication only
    public function authAction(Request $request)
    {
        $redirect = $request->request->get('redirect') == ''
            ? $request->getSession()->get('redirect')
            : $request->request->get('redirect');

        $log = $this->getLogger();

        $session = getSession();
        $session->setAuthenticationFactory(null);

        if ( $session->getUserIt()->getId() == '' )
        {
            $auth_factory = $session->getAuthenticationFactory();
            if ( is_object($log) ) $log->info('Auth factory used: '.get_class($auth_factory));

            if ( $auth_factory->credentialsRequired() )
            {
                $command = new LoginUserService();
                $result = $command->validate(
                    trim($request->request->get('login')),
                    trim($request->request->get('pass'))
                );

                if( $result > 0 ) {
                    if ( is_object($log) ) {
                        $log->info( 'Login used: '.$request->request->get('login') );
                        $log->info( 'Password hash: '.getFactory()->getObject('User')->getHashedPassword(trim($request->request->get('pass'))) );
                    }
                    return $this->replyError( $command->getResultDescription( $result ) );
                }

                $session->open( $command->getUserIt() );
                \SessionBuilder::Instance()->persist(getSession());
            }
            else {
                $command = new LoginUserService();
                return $this->replyRedirectError(
                    '/logoff',
                    $command->getResultDescription(
                        $command->validateUser($auth_factory->authorize())
                    )
                );
            }
        }

        $info = \AuthenticationAppKeyFactory::getKey(getSession()->getUserIt()->getId());
        if ( getSession()->getUserIt()->get('AskChangePassword') == 'Y' ) {
            return $this->replyRedirect(
                '/reset?key='.getSession()->getUserIt()->getResetPasswordKey(),
                '',
                $info
            );
        } else {
            return $this->replyRedirect(
                $redirect != '' ? $redirect : $_SERVER['ENTRY_URL'],
                '',
                $info
            );
        }
    }

    public function authFormAction(Request $request)
    {
        $request->getSession()->set('redirect', \SanitizeUrl::parseUrl($request->request->get('redirect')));

        $session = getSession();
        $session->setAuthenticationFactory(null);

        if ( $session->getUserIt()->getId() == '' )
        {
            $command = new LoginUserService();
            return $this->replyRedirectError(
                '/logoff',
                $command->getResultDescription(
                    $command->validateUser($session->getAuthenticationFactory()->authorize())
                )
            );
        }

        \SessionBuilder::Instance()->persist(getSession());
        $info = \AuthenticationAppKeyFactory::getKey(getSession()->getUserIt()->getId());

        if ( getSession()->getUserIt()->get('AskChangePassword') == 'Y' ) {
            return $this->replyRedirect(
                '/reset?key='.getSession()->getUserIt()->getResetPasswordKey(),
                '',
                $info
            );
        } else {
            return $this->replyRedirect(
                $request->request->get('redirect') != '' ? $request->request->get('redirect') : $_SERVER['ENTRY_URL'],
                '',
                $info
            );
        }
    }

    # region Restore Password
    public function restoreAction(Request $request)
    {
        return $this->responsePage( new \ForgetPasswordPage() );
    }

    public function restoreProcessAction(Request $request)
    {
        if ( $request->request->get('email') == '' ) return $this->replyError(text(219));

        $service = new RestorePasswordService(getFactory(), getSession());
        try {
            return $this->replySuccess(
                $service->execute(strtolower(trim($request->request->get('email'))))
            );
        }
        catch( \Exception $e ) {
            return $this->replyError($e->getMessage());
        }
    }
    #endregion

    # region Reset Password
    function resetAction(Request $request)
    {
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

        $session = getSession();
        $session->setAuthenticationFactory(null);

        $user_it = $user->getAll();
        while ( !$user_it->end() )
        {
            if( trim($request->query->get('key')) == $user_it->getResetPasswordKey() )
            {
                $user->modify_parms($user_it->getId(),
                    array(
                        'Password' => $request->request->get('NewPassword'),
                        'AskChangePassword' => 'N'
                    )
                );

                $session->open( $user_it->copy() );
                \SessionBuilder::Instance()->persist($session);

                if ( $request->request->get('page') != '' ) {
                    return $this->replyRedirect( $request->request->get('page'), text(629) );
                }
                else {
                    return $this->replySuccess( text(629) );
                }
            }

            $user_it->moveNext();
        }

        return $this->replyError( text(233) );
    }
    #endregion

    public function joinAction(Request $request)
    {
        $email = $request->get('email');
        if ( $email == '' ) throw new NotFoundHttpException('Email is required');

        $service = new \Devprom\CommonBundle\Service\Project\InviteService($this, getSession());
        $participant_it = $service->applyInvitation($email);

        if ( $participant_it->getId() < 1 ) throw new NotFoundHttpException('Unable process the invitation');

        return new RedirectResponse(
            \EnvironmentSettings::getServerUrl().
            '/reset?key='.$participant_it->getRef('SystemUser')->getResetPasswordKey().
            '&redirect='.urlencode('/profile?redirect=/pm/'.$participant_it->getRef('Project')->get('CodeName'))
        );
    }

    public function openidAction(Request $request)
    {
        getSession()->setAuthenticationFactory(null);
        $userIt = getSession()->getUserIt();

        if ( $userIt->getId() < 1 ) {
            return new RedirectResponse('/login?redirect=openid');
        }

        getSession()->open( $userIt->copy() );
        \SessionBuilder::Instance()->persist(getSession());

        return new RedirectResponse($_SERVER['ENTRY_URL']);
    }
}