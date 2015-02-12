<?php

namespace Devprom\ServiceDeskBundle\Tests\Service;

use Devprom\ServiceDeskBundle\Service\YamlSettingsStorage;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;
use Devprom\Component\HttpKernel\ServiceDeskAppKernel;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class YamlSettingsStorageTest extends \PHPUnit_Framework_TestCase {

    const SETTINGS_DIR = "@DevpromServiceDeskBundle/Tests/Service/";
    const EXISTING_SETTINGS_FILE_NAME = "test_settings.yml";
    const NON_EXISTING_SETTINGS_FILE_NAME = "doesnt_exist.yml";

    const FIXTURE_SETTINGS_FILE_DATA =
"parameters:
    setting: 'original value'";

    private $existingSettingsFile;

    private $nonExistingSettingsFile;

    public function setUp()
    {
        $kernel = new ServiceDeskAppKernel('test', true);
        $kernel->boot();

        $settingsDir = $kernel->locateResource(self::SETTINGS_DIR);
        $this->existingSettingsFile = $settingsDir . self::EXISTING_SETTINGS_FILE_NAME;
        $this->nonExistingSettingsFile = $settingsDir . self::NON_EXISTING_SETTINGS_FILE_NAME;

        // resetting fixture file
        file_put_contents($this->existingSettingsFile, self::FIXTURE_SETTINGS_FILE_DATA);
    }

    protected function tearDown()
    {
        @unlink($this->existingSettingsFile);
        @unlink($this->nonExistingSettingsFile);
    }


    /**
     * @test
     */
    public function shouldLoadExistingSettings() {
        $storage = new YamlSettingsStorage();

        $settings = $storage->loadSettings($this->existingSettingsFile);

        $this->assertNotEmpty($settings);
        $this->assertEquals('original value', $settings['setting']);
    }

    /**
     * @test
     */
    public function shouldUpdateExistingSettings() {
        $storage = new YamlSettingsStorage();

        $storage->saveSettings(array("setting" => "new value"), $this->existingSettingsFile);

        $updatedSettings = $storage->loadSettings($this->existingSettingsFile);

        $this->assertEquals('new value', $updatedSettings['setting']);
    }

    /**
     * @test
     */
    public function shouldNestStoredSettingsInParametersElement() {
        $storage = new YamlSettingsStorage();

        $storage->saveSettings(array("setting" => "new value"), $this->existingSettingsFile);

        $settingsRaw = Yaml::parse(
            file_get_contents($this->existingSettingsFile)
        );

        $this->assertEquals('new value', $settingsRaw['parameters']['setting']);
    }

    /**
     * @test
     */
    public function shouldCreateNewFileIfItDoesNotExist() {
        $storage = new YamlSettingsStorage();

        $storage->saveSettings(array("setting" => "new value"), $this->nonExistingSettingsFile);

        $updatedSettings = $storage->loadSettings($this->nonExistingSettingsFile);

        $this->assertEquals('new value', $updatedSettings['setting']);
    }



}