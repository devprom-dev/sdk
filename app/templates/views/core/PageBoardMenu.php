<?php
$id = md5(uniqid(time().$title.$random,true));
$modify_item = array_shift(array_values($items));
?>
<div class="btn-group more-actions" data-toggle="tooltip-bottom" title="<?=$hint?>">
    <div class="btn-cell" style="min-width:20px;">
    </div>
    <div class="btn btn-cell transparent-btn" onclick="<?=$modify_item['url']?>">
        <span class="title"><?=$title?></span>
    </div>
    <div class="btn btn-cell dropdown-toggle transparent-btn" data-toggle="dropdown" href="#" data-target="#textmenu<?=$id?>">
        <span class="label">...</span>
    </div>
</div>
<div class="btn-group dropdown-fixed" id="textmenu<?=$id?>">
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>