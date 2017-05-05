<?php

namespace Devprom\ServiceDeskBundle\Security;

use PluginBase;
use PluginsFactory;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class LicenseChecker
{
    private $pluginList;

    public function __construct(PluginsFactory $pluginsFactory)
    {
        $this->pluginList = $pluginsFactory->getNamespaces();
    }

    public function isValid()
    {
        $almPluginList = array_filter($this->pluginList, function (PluginBase $plugin) {
            return strtolower($plugin->getNamespace()) == 'support';
        });
        return !empty($almPluginList) && array_shift($almPluginList)->checkLicense();
    }

}