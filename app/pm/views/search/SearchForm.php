<?php

class SearchForm extends PMForm
{
 	function getAddCaption()
 	{
 		return translate('Поиск');
 	}
 	
 	function getCommandClass()
 	{
 		return 'search';
 	}

	function getAttributes()
	{
		return array('Query'); 	
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Query':
				return 'custom'; 	
		}
	}

 	function getDescription( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Query':
 				return '';
 		}
 	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}
	
	function drawCustomAttribute( $attr, $value, $tab )
	{
	    global $form_id;
	    
		switch ( $attr )
		{
			case 'Query':
			    ?>
			    <script type="text/javascript">

			    function submitSearchRequest()
			    {
			    	var items = new Array();
			    	
			    	jQuery.each($(':checkbox'), function() {
	 					if ( $(this).attr('checked') == 'checked' )
		 					items[items.length] = $(this).attr('id');
	 				});
			    	$('.content-internal form #parameters').val(items.join(','));
			    	
			    	var id = $('.content-internal form').attr('id'); 

			    	$('#action'+id).val(<?=CO_ACTION_PREVIEW?>);
			    }

			    <?php if ( $_REQUEST['quick'] != '' ) { ?>
    			    $(document).ready( function() {
    			    	$('.content-internal form input[type=submit]').click();
    			    });
			    <?php } ?>
			    
			    </script>
			    <?php 
			    
			    $script = "javascript: submitSearchRequest();";
			    
        		echo '<div class="input-append" style="width:100%;">';
          			echo '<input style="width:90%;" name="searchrequest" id="searchrequest" type="text" value="'.htmlentities($_REQUEST['quick'], ENT_QUOTES | ENT_HTML401, 'windows-1251').'">';
          			echo '<input type="submit" class="btn btn-primary" onclick="'.$script.'" value="'.translate('Найти').'">';
        		echo '</div>';

          		echo '<input type="hidden" id="parameters" name="parms">';
        		
				break;
				
			default:
				parent::drawCustomAttribute( $attr, $value, $tab );
		}
	}
	
	function IsCentered()
	{
		return false;
	}
	
	function getWidth()
	{
		return '100%';
	}
	
	function getRenderParms()
	{
	    return array_merge( parent::getRenderParms(), array (
	        'buttons_template' => 'pm/SearchFormButtons.php'
	    ));
	}
}