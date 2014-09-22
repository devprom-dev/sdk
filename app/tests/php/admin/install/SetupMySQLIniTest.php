<?php

class SetupMySQLIniTest extends DevpromTestCase
{
    function testRequiredParametersModified()
    {   
        global $model_factory;
        
        $installable_mock = $this->getMock('SetupMySQLIni', array('getMySQLIniContent', 'writeMySQLIniContent'));
        
        $installable_mock->expects($this->any())->method('getMySQLIniContent')->will( $this->returnValue(
               'max_allowed_packet=1M'.PHP_EOL.
               'ft_min_word_len = 3'.PHP_EOL.
               'lower_case_table_names = 1'.PHP_EOL.
               'group_concat_max_len = 4294967295'.PHP_EOL
        ));
        
        $parameters_keys = array_keys($installable_mock->getParameters());
        $parameters_values = array_values($installable_mock->getParameters());
        
        $installable_mock->expects($this->once())->method('writeMySQLIniContent')
            ->with($this->logicalAnd(
                    $this->stringContains($parameters_keys[0].'='.$parameters_values[0], false),
                    $this->stringContains('lower_case_table_names = 1', false)
                   ));
        
        $installable_mock->install();
    }
}