<?php

class coachingCo extends PluginCoBase
{
    // returns modules of the plugin
    function getModules()
    {
        return array(
            'course' => array(
                'includes' => array( 'coaching/views/RegisterToCoursePage.php' ),
                'classname' => 'RegisterToCoursePage'
            )
        );
    }

    function getCommand( $name )
    {
        switch ( $name ) {
            case 'registercourse':
                return array(
                    'includes' => array( 'coaching/commands/c_registercourse.php' )
                );
        }
    }

	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array();
	}
}