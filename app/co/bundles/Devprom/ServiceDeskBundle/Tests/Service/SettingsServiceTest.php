<?php

namespace Devprom\ServiceDeskBundle\Service;

use PHPUnit_Framework_MockObject_MockObject;
use Devprom\Component\HttpKernel\ServiceDeskAppKernel;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsServiceTest extends \PHPUnit\Framework\TestCase {

    private $kernel;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $storage;

    private $filter;

    private $existingSettingsFile;

    protected function setUp()
    {
        $this->kernel = new ServiceDeskAppKernel('test', true);
        $this->kernel->boot();

        $this->storage =
            $this->getMockBuilder(SettingsStorage::class)
                ->disableOriginalConstructor()
                ->setMethods(["saveSettings", "loadSettings"])
                ->getMock();
        $this->filter =
            $this->getMockBuilder(SettingsFilter::class)
                ->disableOriginalConstructor()
                ->setMethods(["filter"])
                ->getMock();

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
    public function shouldReturnDefaultSettingsWhenNoSettingFileExists() {
        $bundleRelatedFilePath = "@DevpromServiceDeskBundle/Tests/Service/not_existing.yml";

        $service = new SettingsService($this->kernel, $this->storage, $this->filter, $bundleRelatedFilePath, array("setting"));

        $settings = $service->load();
        $this->assertNotEmpty($settings);
        $this->assertArrayHasKey("setting", $settings);
        $this->assertNull($settings['setting']);
    }


}