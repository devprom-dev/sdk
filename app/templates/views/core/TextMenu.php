<?php
$id = md5(uniqid(time().$title.$random,true));
?>
<div class="btn-group" data-toggle="tooltip-bottom" title="<?=$hint?>">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown">
        <span class="title"><?=$title?></span>
        <?php if ( count($items) > 0 ) { ?>
        <span class="caret"></span>
        <?php } ?>
    </div>
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
