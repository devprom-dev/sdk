<?php

namespace Devprom\ServiceDeskBundle\Twig;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class IssueTypeTwigExtention extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('issueTypeIconFilename', function($referenceName)
            {

                switch ($referenceName) {
                    case 'enhancement':
                        $icon = 'layout_edit.png';
                        break;

                    case 'bug':
                        $icon = 'bug.png';
                        break;

                    default:
                        $icon = 'layout_add.png';
                        break;
                }

                return $icon;
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
        return "issueType";
    }

}
