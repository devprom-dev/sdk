<?php

namespace Devprom\ServiceDeskBundle\Tests\Security;

include_once(SERVER_ROOT_PATH . '/core/classes/PluginsFactory.php');

use Devprom\ServiceDeskBundle\Security\LicenseChecker;
use PHPUnit_Framework_TestCase;
use PluginBase;
use PluginsFactory;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class LicenseCheckerTest extends PHPUnit_Framework_TestCase {

    protected function setUp()
    {
    }

    /**
     * @test
     */
    public function shouldFailIfALMPluginDoesntExist() 
    {
        $plugin = $this->getMock("Devprom\ServiceDeskBundle\Tests\Security\TestEEPlugin", array("getNamespace"), array($isLicensed));
        
        $plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("ha-ha"));
    	
        $factory = $this->getMock("\PluginsFactory", array("getNamespaces"), array(), '', false);

    	$factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($plugin) ));
    	
        $checker = new LicenseChecker($factory);

        $this->assertFalse($checker->isValid());
    }

    /**
     * @test
     */
    public function shouldPassIfALMPluginExistAndLicensed() 
    {
        $plugin = $this->getMock("Devprom\ServiceDeskBundle\Tests\Security\TestEEPlugin", array("getNamespace"), array(true));
        
        $plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("EE"));
    	
        $factory = $this->getMock("\PluginsFactory", array("getNamespaces"), array(), '', false);

    	$factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($plugin) ));
    	
        $checker = new LicenseChecker($factory);

        $this->assertTrue($checker->isValid());
    }

    /**
     * @test
     */
    public function shouldIgnorePluginNamespaceCase() 
    {
    	$plugin = $this->getMock("Devprom\ServiceDeskBundle\Tests\Security\TestEEPlugin", array("getNamespace"), array(true));
        
    	$plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("ee"));

        $factory = $this->getMock("\PluginsFactory", array("getNamespaces"), array(), '', false);

    	$factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($plugin) ));
    	
        $checker = new LicenseChecker($factory);

        $this->assertTrue($checker->isValid());
    }


    /**
     * @test
     */
    public function shouldFailIfALMPluginExistButNotLicensed() 
    {
    	$plugin = $this->getMock("Devprom\ServiceDeskBundle\Tests\Security\TestEEPlugin", array("getNamespace"), array(false));
        
    	$plugin->expects($this->any())->method("getNamespace")->will($this->returnValue("ee"));

        $factory = $this->getMock("\PluginsFactory", array("getNamespaces"), array(), '', false);

    	$factory->expects($this->any())->method('getNamespaces')->will( $this->returnValue( array($plugin) ));
    	
        $checker = new LicenseChecker($factory);

        $this->assertFalse($checker->isValid());
    }


}

class TestEEPlugin extends PluginBase {
    private $isLicensed;

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

    function IsLicensed()
    {
        return $this->isLicensed;
    }

    function getSectionPlugins()
    {
        return array();
    }

}