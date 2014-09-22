<?php

namespace Devprom\ServiceDeskBundle\Service;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class YamlSettingsStorage implements SettingsStorage {

    /**
     * @param string $settingsFile
     * @return array
     */
    public function loadSettings($settingsFile) {
        $settings = Yaml::parse(file_get_contents($settingsFile));
        return $settings['parameters'];
    }

    /**
     * @param array $settings
     * @param string $settingsFile
     */
    public function saveSettings($settings, $settingsFile) {
        $settings = array('parameters' => $settings);
        file_put_contents($settingsFile, Yaml::dump($settings));
    }

}