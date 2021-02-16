<?php

namespace Devprom\ServiceDeskBundle\Twig;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsTwigExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    function __construct($em, $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function getGlobals()
    {
        $settings = $this->em->find('Devprom\ServiceDeskBundle\Entity\SystemSettings', 1);
        return array(
            'client_name' => $settings->getClientName(),
            'default_locale' => $settings->getLanguage() == 1 ? 'ru' : 'en',
            'site_url' => $this->router->generate('issue_list', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            'app_version' => md5($_SERVER['APP_VERSION']),
            'support_url' => getFactory()->getObject('HelpDeskSettings')->getAll()->get('appUrl')
        );
    }
}