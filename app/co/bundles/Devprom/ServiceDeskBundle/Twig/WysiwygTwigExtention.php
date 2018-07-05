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
                        new \ParentTransitiveFilter($entity->getId()),
                        new \SortDocumentClause()
                    )
                );

                $html = array();
                while( !$objectIt->end() ) {
                    $parser = new \WrtfCKEditorSupportParser($objectIt->copy(), $container->get('router'));
                    $html[] = '<br/><h4>'.$objectIt->getHtmlDecoded('Caption').'</h4>';
                    $html[] = $parser->parse($objectIt->getHtmlDecoded('Content'));
                    $objectIt->moveNext();
                }
                array_shift($html);

                return join("", $html);
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
