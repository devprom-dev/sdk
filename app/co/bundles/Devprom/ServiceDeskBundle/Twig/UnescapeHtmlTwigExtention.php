<?php


namespace Devprom\ServiceDeskBundle\Twig;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class UnescapeHtmlTwigExtention extends \Twig_Extension {

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('unescapeHtml', 'Devprom\ServiceDeskBundle\Util\TextUtil::unescapeHtml'),
        );
    }

    public function getName()
    {
        return "devprom_unescapeHtml";
    }

}