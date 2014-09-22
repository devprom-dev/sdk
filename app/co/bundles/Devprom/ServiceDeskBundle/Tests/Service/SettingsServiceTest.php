<?php

namespace Devprom\ServiceDeskBundle\Service;
use PHPUnit_Framework_MockObject_MockObject;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsServiceTest extends \PHPUnit_Framework_TestCase {

    private $kernel;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $storage;

    private $filter;

    private $existingSettingsFile;

    protected function setUp()
    {
        $this->kernel = new \ServiceDeskAppKernel('test', true);
        $this->kernel->boot();
        $this->storage = $this->getMock("SettingsStorage", array("saveSettings", "loadSettings"));
        $this->filter = $this->getMock("SettingsFilter", array("filter"));

        $dir = $this->kernel->locateResource("@DevpromServiceDeskBundle/Tests/Service/");
        $this->existingSettingsFile = $dir . "existing.yml";
        file_put_contents($this->existingSettingsFile, "");
    }

    protected function tearDown()
    {
        @unlink($this->existingSettingsFile);
    }


    /**
     * @test
     */
    public function shouldResolveBundleRelatedExistinsFilePaths() {
        $bundleRelatedFilePath = "@DevpromServiceDeskBundle/Tests/Service/existing.yml";

        $this->storage->expects($this->once())->method("loadSettings")
            ->with($this->matchesRegularExpression("/^[^@].+/"));

        $service = new SettingsService($this->kernel, $this->storage, $this->filter, $bundleRelatedFilePath, array("setting"));

        $service->load();
    }


    /**
     * @test
     */
    public function shouldResolveBundleRelatedNonExistinsFilePaths() {
        $bundleRelatedFilePath = "@DevpromServiceDeskBundle/Tests/Service/not_existing.yml";

        $this->storage->expects($this->once())->method("saveSettings")
            ->with($this->anything(), $this->matchesRegularExpression("/^[^@].+/"));

        $service = new SettingsService($this->kernel, $this->storage, $this->filter, $bundleRelatedFilePath, array("setting"));

        $service->save(array("setting" => "new value"));
    }

    /**
     * @test
     */
    public function shouldReturnDefaultSettingsWhenNoSettingFileExists() {
        $bundleRelatedFilePath = "@DevpromServiceDeskBundle/Tests/Service/not_existing.yml";

        $service = new SettingsService($this->kernel, $this->storage, $this->filter, $bundleRelatedFilePath, array("setting"));

        $settings = $service->load();
        $this->assertNotEmpty($settings);
        $this->assertArrayHasKey("setting", $settings);
        $this->assertNull($settings['setting']);
    }


}