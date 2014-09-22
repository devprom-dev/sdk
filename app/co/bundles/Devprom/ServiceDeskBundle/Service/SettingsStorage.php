<?php

namespace Devprom\ServiceDeskBundle\Service;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
interface SettingsStorage {

    /**
     * @return array
     */
    public function loadSettings($settingsFile);

    /**
     * @param array $settings
     */
    public function saveSettings($settings, $settingsFile);

}