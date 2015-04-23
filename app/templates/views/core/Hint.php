<?php

$method = new SettingsWebMethod();

$script = "javscript: ".$method->getJSCall(array('setting' => $name, 'value' => 'off'), "function() { $(this).parent().hide(); } ");

?>

<div class="alert">
  <i class="icon-info" style="background: url(/images/icon-info.png) 0 0 no-repeat;width:28px;height:28px;float: left;"></i>
  <button type="button" class="close" data-dismiss="alert" onclick="<?=$script?>">&times;</button>
  <p><?=$title?></p>
</div>