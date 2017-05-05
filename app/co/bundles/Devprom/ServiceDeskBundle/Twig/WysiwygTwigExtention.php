<?php

namespace Devprom\ServiceDeskBundle\Twig;
use Devprom\ServiceDeskBundle\Util\TextUtil;

class WysiwygTwigExtention extends \Twig_Extension
{
    private $container;

    function __construct($container) {
        $this->container = $container;
    }

    public function getFunctions()
    {
        $container = $this->container;
        return array(
            new \Twig_SimpleFunction('wysiwyg', function($entity) use ($container)
            {
                $registry = getFactory()->getObject('ProjectPage')->getRegistry();
                $registry->setPersisters(array());
                $objectIt = $registry->Query(
                    array(
                        new \FilterInPredicate($entity->getId())
                    )
                );
                $parser = new \WrtfCKEditorSupportParser($objectIt, $container->get('router'));
                return $parser->parse(
                    TextUtil::unescapeHtml($entity->getContent())
                );
            }),
        );
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return "wysiwyg";
    }
}
