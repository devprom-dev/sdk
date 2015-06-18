<?php

class BackupFormApplication extends AdminForm
{
 	function getAddCaption()
 	{
 		return '2. '.translate('Копирование приложения');
 	}
 	
 	function getCommandClass()
 	{
 		return 'backupapplication';
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
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
	    echo '<div class="progress progress-striped active">';
          echo '<div class="bar" style="width:50%;"></div>';
        echo '</div>';

        echo '<input type="hidden" name="parms" value="'.htmlentities($_REQUEST['parms']).'">';
	}
	
	function getTemplate()
	{
	    return 'core/WizardForm.php';
	}
}
 