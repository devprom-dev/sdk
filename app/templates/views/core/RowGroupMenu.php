<?php
$id = md5(uniqid(time().$id,true));
$modify_item = array_shift(array_values($items));
?>
<div class="btn-group row-group">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="#" data-target="#textmenu<?=$id?>">
        <span class="title" ondblclick="<?=$modify_item['url']?>"><?=$title?></span>
        <?php if ( count($items) > 0 ) { ?>
        <span class="caret"></span>
        <?php } ?>
    </div>
</div>
<div class="btn-group dropdown-fixed" id="textmenu<?=$id?>">
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
