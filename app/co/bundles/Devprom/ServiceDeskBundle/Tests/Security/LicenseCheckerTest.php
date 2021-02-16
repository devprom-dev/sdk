<?php

namespace Devprom\ServiceDeskBundle\Tests\Security;

use Devprom\ServiceDeskBundle\Security\LicenseChecker;
use PluginBase;
use PluginsFactory;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class LicenseCheckerTest extends \PHPUnit\Framework\TestCase
{
    private $plugin = null;
    private $factory = null;

    protected function setUp()
    {
        $this->plugin = $this->getMockBuilder(\Devprom\ServiceDeskBundle\Tests\Security\TestEEPlugin::class)
            ->disableOriginalConstructor()
            ->setMethods(["getNamespace"])
            ->getMock();
        $this->factory = $this->getMockBuilder(\PluginsFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(["getNamespaces"])
            ->getMock();
    }

    /**
     * @test
     */
    public function shouldFailIfALMPluginDoesntExist() 
    {
        $this->plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("ha-ha"));
        $this->factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($this->plugin) ));
    	
        $checker = new LicenseChecker($this->factory);
        $this->assertFalse($checker->isValid());
    }

    /**
     * @test
     */
    public function shouldPassIfALMPluginExistAndLicensed() 
    {
        $this->plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("SUPPORT"));
        $this->factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($this->plugin) ));
    	
        $checker = new LicenseChecker($this->factory);
        $this->assertTrue($checker->isValid());
    }

    /**
     * @test
     */
    public function shouldIgnorePluginNamespaceCase() 
    {
        $this->plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("support"));
        $this->factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($this->plugin) ));
    	
        $checker = new LicenseChecker($this->factory);
        $this->assertTrue($checker->isValid());
    }

    /**
     * @test
     */
    public function shouldFailIfALMPluginExistButNotLicensed() 
    {
        $this->plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("ee"));
        $this->factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($this->plugin) ));
    	
        $checker = new LicenseChecker($this->factory);
        $this->assertFalse($checker->isValid());
    }
}

class TestEEPlugin extends PluginBase
{
    private $isLicensed = true;

    public function __construct($isLicensed) 
    {
        parent::__construct();
        $this->isLicensed = $isLicensed;
    }

    static function notLicensed() {
        return new TestEEPlugin(false);
    }

    static function licensed() {
        return new TestEEPlugin(true);
    }

    function getNamespace()
    {
        return 'EE';
    }

    function checkLicense()
    {
        return $this->isLicensed;
    }

    function getSectionPlugins()
    {
        return array();
    }

}