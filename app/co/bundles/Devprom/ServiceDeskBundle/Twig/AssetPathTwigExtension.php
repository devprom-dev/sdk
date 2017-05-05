<?php

namespace Devprom\ServiceDeskBundle\Twig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Routing\Router;
include_once SERVER_ROOT_PATH."admin/classes/templates/SystemTemplate.php";

class AssetPathTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('emailBody', function($fileName, $language) {
                $fileName = basename($fileName);
                if ( strlen($language) != 2 ) $language = 'en';

                $path = \SystemTemplate::getPath().$language.'/'.$fileName;
                if ( !file_exists($path) ) {
                    $path = "Email/".$language."/".$fileName;
                }
                return $path;
            }),
        );
    }

    public function getName() {
        return "assetsPath";
    }
}