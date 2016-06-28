<?php

namespace Devprom\ServiceDeskBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

include_once SERVER_ROOT_PATH."admin/classes/templates/SystemTemplate.php";

class DevpromServiceDeskBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $this->setUpDevpromTranslations();
    }

    public function getParent()
    {
        return "FOSUserBundle";
    }

    protected function setUpDevpromTranslations()
    {
        /** @var Translator $translator */
        $translator = $this->container->get('translator');
        $translator->addResource('php', SERVER_ROOT_PATH . "lang/en/resource.php", "en", "messages");

        // to override branding strings
        $yamls = array(
            'client.en.yml' => 'client',
            'emails.en.yml' => 'emails'
        );
        $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
        foreach( array('en','ru') as $language ) {
            foreach( $yamls as $string => $namespace) {
                $fileName = str_replace('.en.',".".$language.".",$string);
                $filePath = \SystemTemplate::getPath().$language.'/'.$fileName;
                if ( file_exists($filePath) ) {
                    $translator->addResource('yaml', $filePath, $language, $namespace);
                }
                else {
                    $filePath = SERVER_ROOT_PATH."co/bundles/Devprom/ServiceDeskBundle/Resources/baseTranslations/".$fileName;
                    if ( file_exists($filePath) ) {
                        $translator->addResource('yaml', $filePath, $language, $namespace);
                    }
                }
            }
        }

        $en_strings = array (
        		SERVER_ROOT_PATH . "plugins/dobassist/language/en/array.php" => 'client'
        );
        foreach( $en_strings as $string => $namespace) {
        	if ( file_exists($string) ) $translator->addResource('php', $string, "en", $namespace); 
        }
        $ru_strings = array (
        		SERVER_ROOT_PATH . "plugins/dobassist/language/ru/array.php" => 'client'
        );
        foreach( $ru_strings as $string => $namespace ) {
        	if ( file_exists($string) ) $translator->addResource('php', $string, "ru", $namespace); 
        }
    }
}
