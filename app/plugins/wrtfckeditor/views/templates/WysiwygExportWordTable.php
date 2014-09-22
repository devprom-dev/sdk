<?php 

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

$view->extend('core/PageBody.php'); 
    
$view['slots']->output('_content');

?>

<div class="table-header">
	<ul class="breadcrumb">
	    <li>
		    <h2><?=text('wrtfckeditor2')?></h2>
		</li>
	</ul> <!-- end breadcrumb -->
</div>

<div style="width:70%;">
	<?=text('wrtfckeditor3')?>
</div>
<br/>
<div style="width:70%;">
	<img src="/plugins/wrtfckeditor/resources/mswordplugin.png">
</div>
<br/>
<div style="width:70%;">
	<?=text('wrtfckeditor4')?>
</div>


