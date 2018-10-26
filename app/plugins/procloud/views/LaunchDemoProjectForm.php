<?php
 
class LaunchDemoProjectForm extends AjaxForm
{
	function __construct( $object )
	{
		exit(header('Location: http://devprom.ru/module/saasassist/create?template=demo-alm'));
		
		parent::__construct($object);
	}
	
 	function getAddCaption()
 	{
 		return text('procloud1000');
 	}

 	function getCommandClass()
 	{
 		return 'launchdemoproject&namespace=procloud';
 	}

	function getTemplate()
	{
		return "../../plugins/procloud/views/templates/LaunchDemoProjectForm.php";
	}
	
 	function getAttributes()
 	{
 		return array('Progress');
 	}
 	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Progress':
				return 'custom';
		}
	}

	function getSteps()
	{
	    return array( 'total' => 1, 'current' => 1 );
	}
	
	function IsAttributeVisible( $attribute )
	{
		return true; 	
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
	    echo '<div>';
	    	echo '<br/>';
	    	echo text('procloud9');
	    	echo '<br/>';
	    	echo '<br/>';
        echo '</div>';
        
        echo '<input type="hidden" name="template" value="'.htmlentities($_REQUEST['template']).'">';
	}
}
