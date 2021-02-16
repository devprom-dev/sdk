<?php

namespace Devprom\ServiceDeskBundle\Twig;

class WysiwygSingleTwigExtention extends \Twig_Extension
{
    private $container;

    function __construct($container) {
        $this->container = $container;
    }

    public function getFunctions()
    {
        $container = $this->container;
        return array(
            new \Twig_SimpleFunction('singlewysiwyg', function($entity) use ($container)
            {
                $registry = getFactory()->getObject('ProjectPage')->getRegistry();
                $fileRegistry = getFactory()->getObject('WikiPageFile')->getRegistry();

                $registry->setPersisters(array());
                $objectIt = $registry->Query(
                    array(
                        new \FilterInPredicate($entity->getId() > 0 ? $entity->getId() : -1)
                    )
                );

                $html = "";
                $html .= '<span class="icon-pages"></span><h2>'.$objectIt->getHtmlDecoded('Caption').'</h2>';

                $filesHtml = array();
                $fileIt = $fileRegistry->Query(
                    array(
                        new \FilterAttributePredicate('WikiPage', $objectIt->getId())
                    )
                );
                while( !$fileIt->end() ) {
                    $url = $container->get('router')->generate('doc_attachment_download', array('attachmentId' => $fileIt->getId()));
                    $filesHtml[] = '<a href="'.$url.'">'.$fileIt->get('Caption').' ('.$fileIt->getFileSizeKb('Content').' Kb)</a>';
                    $fileIt->moveNext();
                }

                $parser = new \WrtfCKEditorSupportParser($objectIt->copy(), $container->get('router'));
                $html .= join('<br/>', $filesHtml) . $parser->parse($objectIt->getHtmlDecoded('Content'));

                return $html;
            }),
        );
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return "singlewysiwyg";
    }
}
