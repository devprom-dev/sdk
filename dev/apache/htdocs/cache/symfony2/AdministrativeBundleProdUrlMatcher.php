<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * AdministrativeBundleProdUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class AdministrativeBundleProdUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);

        // _admin_entry
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', '_admin_entry');
            }

            return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\MainController::indexAction',  '_route' => '_admin_entry',);
        }

        // _admin_module
        if (preg_match('#^/(?P<module>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_admin_module')), array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\MainController::indexAction',));
        }

        // _admin_namespace
        if (preg_match('#^/(?P<module>[^/]++)/(?P<namespace>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_admin_namespace')), array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\MainController::indexAction',));
        }

        // _admin_page
        if (preg_match('#^/(?P<module>[^/]++)/(?P<namespace>[^/]++)/(?P<page>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_admin_page')), array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\MainController::indexAction',));
        }

        // _admin_install
        if (rtrim($pathinfo, '/') === '/install') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', '_admin_install');
            }

            return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\InstallController::indexAction',  '_route' => '_admin_install',);
        }

        if (0 === strpos($pathinfo, '/admin')) {
            // _admin_license
            if (rtrim($pathinfo, '/') === '/admin/license') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', '_admin_license');
                }

                return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\LicenseController::indexAction',  '_route' => '_admin_license',);
            }

            // _admin_info
            if (rtrim($pathinfo, '/') === '/admin/info') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', '_admin_info');
                }

                return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\InfoController::indexAction',  '_route' => '_admin_info',);
            }

            if (0 === strpos($pathinfo, '/admin/mailer')) {
                // _admin_mailer
                if (rtrim($pathinfo, '/') === '/admin/mailer') {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not__admin_mailer;
                    }

                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_admin_mailer');
                    }

                    return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\MailerController::indexAction',  '_route' => '_admin_mailer',);
                }
                not__admin_mailer:

                // _mailer_store
                if ($pathinfo === '/admin/mailer/') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not__mailer_store;
                    }

                    return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\MailerController::storeAction',  '_route' => '_mailer_store',);
                }
                not__mailer_store:

            }

            // _admin_logs
            if (rtrim($pathinfo, '/') === '/admin/log') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', '_admin_logs');
                }

                return array (  '_controller' => 'Devprom\\AdministrativeBundle\\Controller\\LogsController::indexAction',  '_route' => '_admin_logs',);
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
