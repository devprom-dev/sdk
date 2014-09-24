<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * ApplicationBundleProdUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ApplicationBundleProdUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    static private $declaredRoutes = array(
        '_entry' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::loginAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/',    ),  ),  4 =>   array (  ),),
        'logoff' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::logoffAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/logoff',    ),  ),  4 =>   array (  ),),
        'news' => array (  0 =>   array (    0 => 'key',  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\AtomController::newsAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'key',    ),    1 =>     array (      0 => 'text',      1 => '/news',    ),  ),  4 =>   array (  ),),
        'login' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::loginAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/login',    ),  ),  4 =>   array (  ),),
        'login_process' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::loginProcessAction',  ),  2 =>   array (    '_method' => 'POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/login',    ),  ),  4 =>   array (  ),),
        'recovery' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::restoreAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/recovery',    ),  ),  4 =>   array (  ),),
        'recovery_process' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::restoreProcessAction',  ),  2 =>   array (    '_method' => 'POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/recovery',    ),  ),  4 =>   array (  ),),
        'reset' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::resetAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/reset',    ),  ),  4 =>   array (  ),),
        'reset_process' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\SecurityController::resetProcessAction',  ),  2 =>   array (    '_method' => 'POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/reset',    ),  ),  4 =>   array (  ),),
        'profile' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\ProfileController::formAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/profile',    ),  ),  4 =>   array (  ),),
        'project_new' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\ProjectController::newAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/projects/new',    ),  ),  4 =>   array (  ),),
        'project_create' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\ProjectController::createAction',  ),  2 =>   array (    '_method' => 'POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/projects/new',    ),  ),  4 =>   array (  ),),
        'menu_config' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\MenuController::configAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/menu/config',    ),  ),  4 =>   array (  ),),
        'error_404' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/404',    ),  ),  4 =>   array (  ),),
        'error_500' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/500',    ),  ),  4 =>   array (  ),),
        'error_500_zip' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorZipAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/500.zip',    ),  ),  4 =>   array (  ),),
        'error_310' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/310',    ),  ),  4 =>   array (  ),),
        'error_503' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::errorAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/503',    ),  ),  4 =>   array (  ),),
        '_app_tooltip' => array (  0 =>   array (    0 => 'classname',    1 => 'objects',  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\TooltipController::showAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'objects',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'classname',    ),    2 =>     array (      0 => 'text',      1 => '/tooltip',    ),  ),  4 =>   array (  ),),
        '_namespace' => array (  0 =>   array (    0 => 'module',    1 => 'namespace',  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::moduleAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'namespace',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'module',    ),  ),  4 =>   array (  ),),
        '_page' => array (  0 =>   array (    0 => 'module',    1 => 'namespace',    2 => 'page',  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::moduleAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'page',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'namespace',    ),    2 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'module',    ),  ),  4 =>   array (  ),),
        '_file' => array (  0 =>   array (    0 => 'module',    1 => 'namespace',    2 => 'page',    3 => 'file',  ),  1 =>   array (    '_controller' => 'Devprom\\ApplicationBundle\\Controller\\PageController::moduleAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'file',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'page',    ),    2 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'namespace',    ),    3 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'module',    ),  ),  4 =>   array (  ),),
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context, LoggerInterface $logger = null)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens);
    }
}
