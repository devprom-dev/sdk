<?php 

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

foreach( $commits as $commit ) { ?>

<?php 

$actions = array ( array(), array( 
    'name' => translate('��������'), 
    'url' => $commit['version-url'] 
));

if ( $commit['diff-url'] != '' )
{
    $actions[] = array();
    
    $actions[] = array( 
        'name' => translate('���������'), 
        'url' => $commit['diff-url'] 
    );
}

echo $view->render('core/TextMenu.php', array (
        'title' => $commit['version'].' - '.$commit['author'],
        'items' => $actions
));

?>

<div class="alert alert-info"><?=$commit['comment']?></div>

<?php } ?>