<?php $id = uniqid(); ?>

<a class="btn dropdown-toggle transparent-btn" data-toggle="context" data-target="#context-menu-<?=$id?>" style="padding-left:0">
    <span class="<?=$class?>"><?=$title?></span>
</a>

<div id="context-menu-<?=$id?>">
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
