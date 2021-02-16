<?php
$id = md5(uniqid(time().$title.$random,true));
$modify_item = array_shift(array_values($items));
?>
<div class="btn-group more-actions" data-toggle="tooltip-bottom" title="<?=$hint?>">
    <div class="btn btn-cell transparent-btn" onclick="<?=$modify_item['url']?>">
        <div class="title"><?=$title?></div>
    </div>
    <div class="btn btn-cell dropdown-toggle transparent-btn" data-toggle="dropdown">
        <span class="label">...</span>
    </div>
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
