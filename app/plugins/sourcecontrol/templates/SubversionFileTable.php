<?php 

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

$view->extend('core/PageBody.php'); 

$view['slots']->output('_content');

?>
<link href="/plugins/wrtfckeditor/ckeditor/plugins/codesnippet/lib/highlight/styles/github.css" type="text/css" rel="stylesheet" />
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
</style>

<div class="pull-left" style="width:73%;">
<?

$parts = preg_split('/\//', $path);
$file = $parts[count($parts) - 1];

unset($parts[count($parts) - 1]);
$directory = join('/', $parts);

echo '<h4 class="bs">'.$name.' '. ($version != '' ? ' &nbsp; ['.translate('версия').': '.$version.']' : '').'</h4>';

echo '<pre style="display:block;overflow:auto;">';
    echo '<code>'.Chr(10);
        echo htmlspecialchars($file_body, ENT_COMPAT | ENT_HTML401, 'windows-1251').Chr(10);
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