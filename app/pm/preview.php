<?php

 require_once('common.php');
 require_once('views/wiki/parsers/WikiHtmlParser.php');
 
 header('Content-Type: text/html; charset='.APP_ENCODING);
 
 echo '<html>';
?>
<head>
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$_SERVER['APP_VERSION']?>&type=css"/>
</head>
<?
 echo '<body style="background:white;">';
 echo '<div style="padding:10pt;">';

 if ( $_REQUEST['content'] != '' )
 {
 	if ( $_REQUEST['wiki_id'] > 0 )
 	{
	 	$wiki = $model_factory->getObject('WikiPage');
	 	$wiki_it = $wiki->getExact($_REQUEST['wiki_id']);
 	}
 	elseif ( $_REQUEST['post_id'] )
 	{
	 	$wiki = $model_factory->getObject('BlogPost');
	 	$wiki_it = $wiki->getExact($_REQUEST['post_id']);
 	}

	$parser = new WikiHtmlParser( $wiki_it );
	$parser->setObjectIt( $wiki_it );
	
	echo $parser->parse( IteratorBase::utf8towin(str_replace(chr(10),chr(13).chr(10),$_REQUEST['content'])) );
 }
  
 echo '</div>';
 echo '</body>';
 echo '</html>';
 
  
?>