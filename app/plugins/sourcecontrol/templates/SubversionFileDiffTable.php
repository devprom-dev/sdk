<?php 

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

$view->extend('core/PageBody.php'); 

$view['slots']->output('_content');

?>
<link href="/scripts/prettify/prettify.css" type="text/css" rel="stylesheet" />
<style>
	.str { color:#181; font-style:italic }
	.kwd { color:#369 }
	.com { color:#666 }
	.typ { color:#c40 }
	.lit { color:#900 }
	.pun { color:#000; font-weight:bold  }
	.pln { color:#333 }
	.tag { color:#369; font-weight:bold  }
	.atn { color:#939; font-weight:bold  }
	.atv { color:#181 }
	.dec { color:#606 }
	.src { background:black;color:white; }
	tt {display:none;}
	.original {background:#FFD1C5;}
	.final {background:#D4FFC5;}
	.added {background:#D4FFC5;}
</style>

<script type="text/javascript" src="/scripts/prettify/prettify.js"></script>

<script language="javascript">
	$(document).ready(function() 
	{ 
		markupDiff($('.prettyprint'));
		prettyPrint();
	});
</script>

<div class="pull-left" style="width:73%;">
<?php 

echo '<h4 class="bs">'.$name.' '.($version != '' ? ' &nbsp; ['.translate('версии').': '.$version.' - '.$preversion.']' : '').'</h4>';

echo '<pre style="display:block;overflow:auto;">';
echo '<code class="prettyprint">';

echo $file_body;

echo '</code>';
echo '</pre>';

?>
</div>

<div class="pull-right span3">
<?php 

echo $view->render('core/PageSections.php', array(
        'sections' => $sections,
        'object_class' => $object_class,
        'object_id' => $object_id
));

?>
</div>