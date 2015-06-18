<?php

include 'DevpromLicenseForm.php';
include 'DevpromLicenseReviewForm.php';

class LicenseDEVPROMTable extends BaseDEVPROMTable
{
	function validate()
	{
		global $user_it, $_REQUEST;
		
		if ( !$user_it->IsReal() )
		{
			$this->script = '<script type="text/javascript">var licenseUrl = '.JsonWrapper::encode($_SERVER['REQUEST_URI'].'&key=login').'; $().ready(function(){getLicense(licenseUrl);});</script>';
		}
		else
		{
			if ( $_REQUEST['key'] == '' )
			{
				getSession()->close();
				
				exit(header('Location: '.$_SERVER['REQUEST_URI'].'&key=login'));
			}
			
			$this->script = '';
		}
	}
 	
	function getForm()
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( $_REQUEST['key'] == 'show' )
 		{
 		    return new DevpromLicenseReviewForm( $model_factory->getObject('cms_License') );
 		}
 		else
 		{
 			return new DevpromLicenseForm( $model_factory->getObject('cms_License') );
 		}
 	}
 	
	function draw()
	{
		ob_start();
		
		parent::draw();
		
		$page_content = ob_get_contents();
		
		ob_end_clean();

		if ( $this->script != '' )
		{
			echo str_replace('<%page%>', '', $page_content);
			
			return;
		}
		
		ob_start();
		
		$form = $this->getForm();
		
		$form->draw();
		
		$form_content = ob_get_contents();
		
		ob_end_clean();
		
		$page_content = str_replace('<%page%>', $form_content, $page_content);
		
		echo $page_content;
	}
}