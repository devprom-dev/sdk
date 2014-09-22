<?php

class UpgradeCustomerUIDLocationTest extends DevpromTestCase
{
    function testAbsentParameter()
    {   
        global $model_factory;
        
        $installable_mock = $this->getMock('UpgradeCustomerUIDLocation', array('getSettingsContent', 'writeSettingsContent'));
        
        $installable_mock->expects($this->any())->method('getSettingsContent')->will( $this->returnValue(
               "define('SERVER_FILES_PATH', SERVER_ROOT.'/files/');".PHP_EOL.
               "define('CACHE_PATH', '/cache');".PHP_EOL
        ));
        
        define( 'CUSTOMER_UID', 'asd' );
        
        $installable_mock->expects($this->once())->method('writeSettingsContent')
            ->with( $this->stringContains("define('CUSTOMER_UID', 'asd');", false) );
        
        $installable_mock->install();
    }

    function testExistParameter()
    {   
        global $model_factory;
        
        $installable_mock = $this->getMock('UpgradeCustomerUIDLocation', array('getSettingsContent', 'writeSettingsContent'));
        
        $installable_mock->expects($this->any())->method('getSettingsContent')->will( $this->returnValue(
               "define('SERVER_FILES_PATH', SERVER_ROOT.'/files/');".PHP_EOL.
               "define('CACHE_PATH', '/cache');".PHP_EOL.
               "define('CUSTOMER_UID', 'bsd');"
        ));
        
        define( 'CUSTOMER_UID', 'asd' );
        
        $installable_mock->expects($this->once())->method('writeSettingsContent')
            ->with( $this->logicalAnd(
                        $this->stringContains("define('CACHE_PATH', '/cache');", false),
                        $this->logicalNot($this->stringContains("define('CUSTOMER_UID', 'asd');", false))
                    ));
        
        $installable_mock->install();
    }
}