<?php

 ///////////////////////////////////////////////////////////////////////////////
 class SubmitWikiWebMethod extends WebMethod
 {
 	var $cache_name, $method;
 	
 	function SubmitWikiWebMethod( $cache_name = '', $method = '')
 	{
 		$this->cache_name = $cache_name;
 		$this->method = $method;
 		
 		parent::WebMethod();
 	}
 	
 	function execute_request() 
 	{
 		global $_REQUEST;
 		$this->execute($_REQUEST);
 	}
 	
 	function drawButton( $parms_array ) 
 	{
 		global $submitwebmethod_number;
 		
 		$text_id = 'select_';
 		if( $submitwebmethod_number < 1 )
 		{
 		?>
	 	<script language="javascript">
	 		function submit_<? echo get_class($this) ?>( method_id, url )
	 		{
		        var http;
		        if (typeof ActiveXObject != 'undefined') {
		            http = new ActiveXObject('Microsoft.XMLHTTP');
		        } else if (typeof XMLHttpRequest != 'undefined') {
		            http = new XMLHttpRequest();
		        } else {
		            return false;
		        }
		        var sel = document.getElementById('<? echo $this->cache_name; ?>');
	 			
	 			$('#select_'+method_id).attr('disabled', true);
	 			
		        http.open("GET", url+"&value="+sel.value, false);
		        http.send(null);
		        if( http.responseText != '' ) {
					window.location = http.responseText;
		        }
	 			return;
	 		}
	 	</script>
	 	<?
 		}
 		
 		$submitwebmethod_number += 1;
 		$url = $this->method->getUrl( $parms_array );
 		
 		$method_id = $submitwebmethod_number;
 		$text_id = 'select_'.$method_id;
 		?>
		<input type="button" value="<? echo_lang('Продолжить') ?>" id="<? echo $text_id ?>" 
			class="button" onclick="javascript: submit_<? echo get_class($this) ?>( <? echo $method_id ?>, '<? echo $url ?>' )">
		<?
 	}
 }
