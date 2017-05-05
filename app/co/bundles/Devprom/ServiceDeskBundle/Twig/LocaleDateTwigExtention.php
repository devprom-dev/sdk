<?php

namespace Devprom\ServiceDeskBundle\Twig;

class LocaleDateTwigExtention extends \Twig_Extension
{
    private $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('localeDate', function($dateValue)
            {
                if ( !is_object($dateValue) ) return '';
                switch ($this->container->get('request')->getLocale()) {
                    case 'ru':
                        return $dateValue->format("d.m.Y H:i");
                    default:
                        return $dateValue->format("m/d/Y H:i");
                }
            }),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "localeDate";
    }

}
