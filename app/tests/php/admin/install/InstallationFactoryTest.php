<?php

class InstallationFactoryTest extends DevpromTestCase
{
    function testFakeInstallableExecuted()
    {
        $installable = $this->getMock('Installable', array('install', 'check', 'skip'));

	    $installable->expects($this->once())->method('skip')->will($this->returnValue( false ));
	    
        $installable->expects($this->once())->method('check')->will($this->returnValue( true ));
        
	    $installable->expects($this->once())->method('install')->will($this->returnValue( true ));
	    
	    $class = $this->getMockClass("InstallationFactory", array("buildInstallables"));
	    
		$class::staticExpects($this->any())->method('buildInstallables')->will($this->returnValue(
	            array( $installable ) 
	    ));
	    
	    $factory = $class::getFactory();
	    	    
	    $issues = array();
	    
	    $factory->install( $issues );
	    
	    $this->assertEquals( 0, count($issues) );
    }
}