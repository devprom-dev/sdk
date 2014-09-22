<?php 

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

$view->extend('core/PageBody.php'); 

$view['slots']->output('_content');

foreach( $connectors as $connector )
{
    echo '<b>Path: '.$connector['url'].'/'.$connector['path'].'</b>';
    echo '<br/>';
    
    echo 'LoginName: '.$connector['login'];
    echo '<br/>';
    echo '<br/>';
     
    echo $connector['debug'];
    
    echo '<br/>';
    echo '<br/>';
}

?>