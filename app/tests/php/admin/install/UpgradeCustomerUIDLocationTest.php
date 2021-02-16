<?php

class UpgradeCustomerUIDLocationTest extends DevpromTestCase
{
    private $locator = null;

    function setUp() {
        parent::setUp();
        $this->locator = $this->getMockBuilder(\UpgradeCustomerUIDLocation::class)
            ->setConstructorArgs(array())
            ->setMethods(['getSettingsContent', 'writeSettingsContent'])
            ->getMock();
    }

    function testAbsentParameter()
    {   
        $this->locator->expects($this->any())->method('getSettingsContent')->will( $this->returnValue(
               "define('SERVER_FILES_PATH', SERVER_ROOT.'/files/');".PHP_EOL.
               "define('CACHE_PATH', '/cache');".PHP_EOL
        ));
        
        define( 'CUSTOMER_UID', 'asd' );

        $this->locator->expects($this->once())->method('writeSettingsContent')
            ->with( $this->stringContains("define('CUSTOMER_UID', 'asd');", false) );

        $this->locator->install();
    }

    function testExistParameter()
    {   
        $this->locator->expects($this->any())->method('getSettingsContent')->will( $this->returnValue(
               "define('SERVER_FILES_PATH', SERVER_ROOT.'/files/');".PHP_EOL.
               "define('CACHE_PATH', '/cache');".PHP_EOL.
               "define('CUSTOMER_UID', 'bsd');"
        ));
        
        define( 'CUSTOMER_UID', 'asd' );

        $this->locator->expects($this->once())->method('writeSettingsContent')
            ->with( $this->logicalAnd(
                        $this->stringContains("define('CACHE_PATH', '/cache');", false),
                        $this->logicalNot($this->stringContains("define('CUSTOMER_UID', 'asd');", false))
                    ));

        $this->locator->install();
    }
}