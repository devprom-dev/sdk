<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * ApplicationBundleProdUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ApplicationBundleProdUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
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

        // _entry
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', '_entry');
            }

            return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::loginAction',  '_route' => '_entry',);
        }

        // logoff
        if ($pathinfo === '/logoff') {
            return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::logoffAction',  '_route' => 'logoff',);
        }

        // news
        if (0 === strpos($pathinfo, '/news') && preg_match('#^/news/(?P<key>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'news')), array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\AtomController::newsAction',));
        }

        if (0 === strpos($pathinfo, '/login')) {
            // login
            if ($pathinfo === '/login') {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_login;
                }

                return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::loginAction',  '_route' => 'login',);
            }
            not_login:

            // login_process
            if ($pathinfo === '/login') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_login_process;
                }

                return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::loginProcessAction',  '_route' => 'login_process',);
            }
            not_login_process:

        }

        if (0 === strpos($pathinfo, '/re')) {
            if (0 === strpos($pathinfo, '/recovery')) {
                // recovery
                if ($pathinfo === '/recovery') {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_recovery;
                    }

                    return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::restoreAction',  '_route' => 'recovery',);
                }
                not_recovery:

                // recovery_process
                if ($pathinfo === '/recovery') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_recovery_process;
                    }

                    return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::restoreProcessAction',  '_route' => 'recovery_process',);
                }
                not_recovery_process:

            }

            if (0 === strpos($pathinfo, '/reset')) {
                // reset
                if ($pathinfo === '/reset') {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_reset;
                    }

                    return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::resetAction',  '_route' => 'reset',);
                }
                not_reset:

                // reset_process
                if ($pathinfo === '/reset') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_reset_process;
                    }

                    return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::resetProcessAction',  '_route' => 'reset_process',);
                }
                not_reset_process:

            }

        }

        if (0 === strpos($pathinfo, '/pro')) {
            // profile
            if ($pathinfo === '/profile') {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_profile;
                }

                return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\ProfileController::formAction',  '_route' => 'profile',);
            }
            not_profile:

            if (0 === strpos($pathinfo, '/projects/new')) {
                // project_new
                if ($pathinfo === '/projects/new') {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_project_new;
                    }

                    return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\ProjectController::newAction',  '_route' => 'project_new',);
                }
                not_project_new:

                // project_create
                if ($pathinfo === '/projects/new') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_project_create;
                    }

                    return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\ProjectController::createAction',  '_route' => 'project_create',);
                }
                not_project_create:

            }

        }

        // menu_config
        if ($pathinfo === '/menu/config') {
            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                $allow = array_merge($allow, array('GET', 'HEAD'));
                goto not_menu_config;
            }

            return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\MenuController::configAction',  '_route' => 'menu_config',);
        }
        not_menu_config:

        // error_404
        if ($pathinfo === '/404') {
            return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  '_route' => 'error_404',);
        }

        if (0 === strpos($pathinfo, '/500')) {
            // error_500
            if ($pathinfo === '/500') {
                return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  '_route' => 'error_500',);
            }

            // error_500_zip
            if ($pathinfo === '/500.zip') {
                return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorZipAction',  '_route' => 'error_500_zip',);
            }

        }

        // error_310
        if ($pathinfo === '/310') {
            return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  '_route' => 'error_310',);
        }

        // error_503
        if ($pathinfo === '/503') {
            return array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  '_route' => 'error_503',);
        }

        // _app_tooltip
        if (0 === strpos($pathinfo, '/tooltip') && preg_match('#^/tooltip/(?P<classname>[^/]++)/(?P<objects>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_app_tooltip')), array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\TooltipController::showAction',));
        }

        // _namespace
        if (preg_match('#^/(?P<module>[^/]++)/(?P<namespace>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_namespace')), array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::moduleAction',));
        }

        // _page
        if (preg_match('#^/(?P<module>[^/]++)/(?P<namespace>[^/]++)/(?P<page>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_page')), array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::moduleAction',));
        }

        // _file
        if (preg_match('#^/(?P<module>[^/]++)/(?P<namespace>[^/]++)/(?P<page>[^/]++)/(?P<file>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => '_file')), array (  '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::moduleAction',));
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
