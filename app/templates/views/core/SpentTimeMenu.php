<?php
$id = md5(uniqid(time().$id,true));
?>
<div class="btn-group">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="#" style="padding:0;" data-target="#textmenu<?=$id?>">
        <span class="title"><?=$title?></span>
    </div>
</div>
<div class="btn-group dropdown-fixed" id="textmenu<?=$id?>">
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
