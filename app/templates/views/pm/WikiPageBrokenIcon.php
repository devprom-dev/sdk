<?php 

$method = "javascript: runMethod('".$url."methods.php?method=OpenBrokenTraceWebMethod', {'object' : '".$id."'}, '', '');";
$tooltip_url = $url.'tooltip/explain/'.$id;

?>
<a class="with-tooltip" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="<?=$tooltip_url?>" href="<?=$method?>"><img class="trace-state" src="/images/exclamation.png"></a>