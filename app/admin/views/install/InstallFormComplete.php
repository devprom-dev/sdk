<?php

class InstallFormComplete extends AdminForm
{
 	function getAddCaption()
 	{
 		return '2. '.text(1361);
 	}
 	
 	function getCommandClass()
 	{
 		return 'installcomplete';
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

	function IsAttributeVisible( $attribute )
	{
		return true; 	
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
	    echo '<div class="progress progress-striped active">';
          echo '<div class="bar" style="width:50%;"></div>';
        echo '</div>';
	}
	
	function getTemplate()
	{
	    return 'core/WizardForm.php';
	}
}
 