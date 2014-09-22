<?php

namespace Devprom\ServiceDeskBundle\Twig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Routing\Router;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsTwigExtension extends \Twig_Extension
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    function __construct($entityManager, $router)
    {
        $this->em = $entityManager;
        $this->router = $router;
    }


    public function getGlobals()
    {
        $settings = $this->em->find('Devprom\ServiceDeskBundle\Entity\SystemSettings', 1);

        return array(
            'client_name' => $settings->getClientName(),
            'site_url' => $this->router->generate('issue_list', array(), true),
        );
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "settings";
    }

}