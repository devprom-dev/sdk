<?php
if ( $title == '' ) return;

$hideMethod = new SettingsWebMethod();
$hideMethod->setRedirectUrl("function() { $(this).parent().hide(); } ");
$hideScript = "javscript: ".$hideMethod->getJSCall(array('setting' => $name, 'value' => 'off'));

$showMethod = new SettingsWebMethod();
$showMethod->setRedirectUrl("function() { $('.alert-hint').removeClass('hidden'); $('.hint-open-link').parent().hide(); } ");
$showScript = "javscript: ".$showMethod->getJSCall(array('setting' => $name, 'value' => 'on'));
?>

<? if ( !$open ) { ?>
<div class="hint-container">
  <i class="icon icon-question-sign"></i><a class="btn btn-link hint-open-link" onclick="<?=$showScript?>"><?=text(2220)?></a>
</div>
<? } ?>
<div class="clearfix"></div>
<div class="alert alert-hint <?=($open ? "" : "hidden")?>">
  <i class="icon-info" style="background: url(/images/icon-info.png) 0 0 no-repeat;width:28px;height:28px;float: left;"></i>
  <button type="button" class="close" data-dismiss="alert" onclick="<?=$hideScript?>">
    <span style="font-size:13px;vertical-align:top"><?=translate('закрыть')?></span> &times;
  </button>
  <p><?=$title?></p>
</div>