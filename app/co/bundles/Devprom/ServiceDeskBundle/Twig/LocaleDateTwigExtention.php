<?php

namespace Devprom\ServiceDeskBundle\Twig;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleDateTwigExtention extends \Twig_Extension
{
    private $stack;

    function __construct(RequestStack $stack)
    {
        $this->stack = $stack;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('localeDate', function($dateValue)
            {
                if ( !is_object($dateValue) ) return '';
                switch ($this->stack->getCurrentRequest()->getLocale()) {
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
