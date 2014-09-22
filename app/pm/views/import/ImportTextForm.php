<?php

class ImportTextForm extends PMForm
{
 	function getAddCaption()
 	{
 		return text(372);
 	}
 	
 	function getCommandClass()
 	{
 		return 'requestsimport';
 	}

	function getAttributes()
	{
		return array('Excel'); 	
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return text(946); 	
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return 'text'; 	
		}
	}

 	function getDescription( $attribute )
 	{
 		return text(278);
 	}

	function IsAttributeRequired( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return true; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function getButtonText()
	{
		return translate('Импортировать');
	}

 	function getRedirectUrl()
	{
		return '';
		
		switch ( $this->getAction() )
		{
			case CO_ACTION_CREATE:
				return 'requests.php'; 
		}
	}
	
	function getWidth()
	{
		return '100%';
	}

	function IsPreviewEnabled()
	{
		return true;
	}
	
	function IsCentered()
	{
		return false;
	}
	
	function draw()
	{
		parent::draw();
		
		?>
		<script type="text/javascript">
			$().ready(function() {
				$('.ajax_form textarea').css('height', '300px');
			});
		</script>
		<?php 
	}
}
