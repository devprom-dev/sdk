<?php
$id = md5(uniqid(time().$title.$random,true));
?>
<div class="btn-group">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="#" data-target="#textmenu<?=$id?>">
        <span class="title"><?=$title?></span>
        <?php if ( count($items) > 0 ) { ?>
        <span class="caret"></span>
        <?php } ?>
    </div>
</div>
<div class="btn-group dropdown-fixed" id="textmenu<?=$id?>">
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
