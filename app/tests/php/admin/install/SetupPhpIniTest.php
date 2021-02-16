<?php

class SetupPhpIniTest extends DevpromTestCase
{
    private $command = null;

    function setUp() {
        parent::setUp();
        $this->command = $this->getMockBuilder(\SetupPhpIni::class)
            ->setConstructorArgs(array())
            ->setMethods(['getPhpIniContent', 'writePhpIniContent'])
            ->getMock();
    }

    function testMissingExtensionsAppended()
    {   
        $this->command->expects($this->any())->method('getPhpIniContent')->will( $this->returnValue(
               '[PHP]'.PHP_EOL.
               'extension_dir="../php/extensions"'.PHP_EOL.
               'upload_max_filesize=20M'.PHP_EOL.
               'error_reporting=E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT'.PHP_EOL.
               'extension=php_mysql.dll'.PHP_EOL.         
               'extension=php_gd2.dll'.PHP_EOL.         
               'extension=php_openssl.dll'.PHP_EOL.         
               'extension=php_curl.dll'.PHP_EOL.         
               'extension=php_mbstring.dll'.PHP_EOL.         
               ';extension=eAccelerator_ts.dll'.PHP_EOL.
               'extension=php_ldap.dll'.PHP_EOL.
               'extension=php_imap.dll'.PHP_EOL.
               'zend_extension="php_xdebug-2.2.3-5.5-vc11.dll"'.PHP_EOL
        ));

        $this->command->expects($this->once())->method('writePhpIniContent')
            ->with($this->logicalAnd(
                    $this->stringContains('extension=php_pdo_mysql.dll', false),
                    $this->stringContains('extension=php_fileinfo.dll', false),
                    $this->stringContains('extension=php_imap.dll', false),
                    $this->logicalNot($this->stringContains('extension=php_oracle.dll', false))
                   ));

        $this->command->install();
    }
}