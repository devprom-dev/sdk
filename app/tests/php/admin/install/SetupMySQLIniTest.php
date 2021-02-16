<?php

class SetupMySQLIniTest extends DevpromTestCase
{
    private $command = null;

    function setUp() {
        parent::setUp();
        $this->command = $this->getMockBuilder(\SetupMySQLIni::class)
            ->setConstructorArgs(array())
            ->setMethods(['getMySQLIniContent', 'writeMySQLIniContent'])
            ->getMock();
    }

    function testRequiredParametersModified()
    {   
        $this->command->expects($this->any())->method('getMySQLIniContent')->will( $this->returnValue(
               'max_allowed_packet=1M'.PHP_EOL.
               'ft_min_word_len = 3'.PHP_EOL.
               'lower_case_table_names = 1'.PHP_EOL.
               'group_concat_max_len = 4294967295'.PHP_EOL
        ));
        
        $parameters_keys = array_keys($this->command->getParameters());
        $parameters_values = array_values($this->command->getParameters());

        $this->command->expects($this->once())->method('writeMySQLIniContent')
            ->with($this->logicalAnd(
                    $this->stringContains($parameters_keys[0].'='.$parameters_values[0], false),
                    $this->stringContains('lower_case_table_names = 1', false)
                   ));

        $this->command->install();
    }
}