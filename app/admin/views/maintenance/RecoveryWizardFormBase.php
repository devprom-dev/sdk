<?php

class RecoveryWizardFormBase extends AdminForm
{
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
	    return array( 'total' => 0, 'current' => 0 );
	}
	
	function IsAttributeVisible( $attribute )
	{
		return true; 	
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
	    $steps = $this->getSteps();
	    
	    $step_width = round($steps['current'] * 100 / $steps['total'], 0);
	    
	    echo '<div class="progress progress-striped active">';
          echo '<div class="bar" style="width: '.$step_width.'%;"></div>';
        echo '</div>';
        
        echo '<input type="hidden" name="parms" value="'.htmlentities($_REQUEST['parms']).'">';
	}
	
	function getTemplate()
	{
	    return 'core/WizardForm.php';
	}
}