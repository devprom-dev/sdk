<?php $id = uniqid(); ?>

<a class="btn dropdown-toggle transparent-btn" data-toggle="context" data-target="#context-menu-<?=$id?>" href="javascript: gotoRandomPage(<?=$page_id?>, 3, true)" style="padding-left:0">
    <span class="title <?=$class?>"><?=$title?></span>
</a>

<div id="context-menu-<?=$id?>">
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
