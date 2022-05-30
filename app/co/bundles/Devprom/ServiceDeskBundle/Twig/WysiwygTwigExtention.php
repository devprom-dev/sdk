<?php

namespace Devprom\ServiceDeskBundle\Twig;

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
                $fileRegistry = getFactory()->getObject('WikiPageFile')->getRegistry();

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
                    $html[] = '<br/><h4 id="'.$objectIt->getId().'">'.$objectIt->getHtmlDecoded('Caption').'</h4>';

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

                    $html[] = join('<br/>', $filesHtml) . $parser->parse($objectIt->getHtmlDecoded('Content'));
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
