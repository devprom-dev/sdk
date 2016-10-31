<?php

$method = new SettingsWebMethod();

$script = "javscript: ".$method->getJSCall(array('setting' => $name, 'value' => 'off'), "function() { $(this).parent().hide(); } ");

?>

<div class="alert alert-hint">
  <button type="button" class="close" data-dismiss="alert" onclick="<?=$script?>">&times;</button>
  <?=$title?>
</div>