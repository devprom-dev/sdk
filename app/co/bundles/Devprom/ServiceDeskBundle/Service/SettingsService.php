<?php

namespace Devprom\ServiceDeskBundle\Service;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsService {

    /** @var  Kernel */
    private $kernel;

    /** @var  SettingsStorage */
    private $settingsStorage;

    /** @var  SettingsFilter */
    private $settingsFilter;

    private $defaultSettings;

    private $settingsFilePath;

    function __construct(Kernel $kernel, $settingsStorage, $settingsFilter, $settingsFilePath, $acceptedSettings)
    {
        $this->kernel = $kernel;
        $this->settingsFilter = $settingsFilter;
        $this->settingsStorage = $settingsStorage;
        $this->settingsFilePath = $settingsFilePath;
        $defaultSettings = array();
        foreach ($acceptedSettings as $setting) {
            $defaultSettings[$setting] = null;
        };
        $this->defaultSettings = $defaultSettings;


        if (substr($settingsFilePath, 0, 1) === "@") {
            try {
                $this->settingsFilePath = $this->kernel->locateResource($settingsFilePath);
            } catch (\InvalidArgumentException $e) {
                // resource doesn't exist. Let's construct full path manually
                $this->settingsFilePath = preg_replace("/^@.+?(?=[\\/])/", $kernel->getRootDir(), $this->settingsFilePath);
            }
        }

    }

    /**
     * @param array $settings
     */
    public function save($settings) {
        $this->settingsFilter->filter($settings);
        $this->settingsStorage->saveSettings($settings, $this->settingsFilePath);
        $this->resetCache();
    }

    /**
     * @return array
     */
    public function load() {
        if (file_exists($this->settingsFilePath)) {
            return $this->settingsStorage->loadSettings($this->settingsFilePath);
        } else {
            return $this->defaultSettings;
        }
    }
    
    public function resetCache()
    {
    	$command = new \Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
    	$command->setContainer($this->kernel->getContainer()); 
    	
    	$output = new \Symfony\Component\Console\Output\NullOutput();
    	$command->run(new \Symfony\Component\Console\Input\ArgvInput(array('', '--no-warmup')), $output);
    }
}