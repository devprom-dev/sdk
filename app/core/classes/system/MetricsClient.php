<?php

class MetricsClient
{
	private static $startTime = null;
	
	public function Start()
	{
		if ( !is_null($this->startTime) ) return;
		
		$this->startTime = microtime(true);
	}
	
	public function getDuration( $element_id )
	{
	 	?>
	 	<script type="text/javascript">
		    $(document).ready(function() 
		    {
		 		var clientStartTime = <?php echo round($this->startTime * 1000) ?>;
			    var clientElapsedTime = new Date().getTime() - clientStartTime; 
			    $('#<?=$element_id?>').html((clientElapsedTime / 1000.0));
			}); 
	 	</script>
	 	<?php 
	}

    protected static $singleInstance = null;
    
    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        
        static::$singleInstance = new static();

        return static::$singleInstance;
    }
    
    private function __construct() {}
}