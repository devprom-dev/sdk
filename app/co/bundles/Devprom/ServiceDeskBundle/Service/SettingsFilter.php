<?php

namespace Devprom\ServiceDeskBundle\Service;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsFilter {

    private $acceptedSettings;

    function __construct($acceptedSettings)
    {
        $this->acceptedSettings = $acceptedSettings;
    }

    /**
     * @param array $settings
     * @return array
     */
    public function filter($settings) {
        $acceptedSettings = $this->acceptedSettings;

        $rejectedSettings = array_filter(array_keys($settings), function($submittedSetting) use ($acceptedSettings) {
            return !in_array($submittedSetting, $acceptedSettings);
        });

        array_walk($rejectedSettings, function ($rejectedSetting) use(&$settings) {
            unset($settings[$rejectedSetting]);
        });

        return $settings;
    }

}