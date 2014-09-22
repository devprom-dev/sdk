<?php

class BackupFormComplete extends AdminForm
{
 	function getAddCaption()
 	{
 		return '3. '.translate('Подготовка резервной копии');
 	}
 	
 	function getCommandClass()
 	{
 		return 'backupcomplete';
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
          echo '<div class="bar" style="width:75%;"></div>';
        echo '</div>';
        
        echo '<input type="hidden" name="parms" value="'.htmlentities($_REQUEST['parms']).'">';
	}
	
	function getTemplate()
	{
	    return 'core/WizardForm.php';
	}
}
 