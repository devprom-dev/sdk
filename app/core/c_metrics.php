<?php
 
 class SystemMetrics
 {
 	function startScript()
 	{
 	}
 	
 	function getScriptExecutionDuration( $element_id )
 	{
 	}
 }
 
 class ServerMetrics extends SystemMetrics
 {
 	var $start_time;
 	
 	function startScript()
 	{
 		$this->start_time = microtime(true);
 	}

  	function getScriptExecutionDuration( $element_id )
 	{
 		return round(microtime(true) - $this->start_time, 3);
 	}
 }
 
 class ClientMetrics extends SystemMetrics
 {
 	var $start_time;
 	
 	function startScript()
 	{
 		$this->start_time = microtime(true);
 	}

  	function getScriptExecutionDuration( $element_id )
 	{
 	?>
 	<script type="text/javascript">
	    $(document).ready(function() 
	    {
	 		var clientStartTime = <?php echo round($this->start_time * 1000) ?>;
		    var clientElapsedTime = new Date().getTime() - clientStartTime; 
		    $('#<?php echo $element_id ?>').html((clientElapsedTime / 1000.0));
		}); 
 	</script>
 	<?php 
 	}
 }
 
?>