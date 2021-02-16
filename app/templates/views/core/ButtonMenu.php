<div class="btn-group pull-left last">
    <a class="btn btn-sm dropdown-toggle btn-info" uid="<?=$uid?>" data-toggle="dropdown">
        <?=$title?>
        <span class="caret"></span>
    </a>
    <? echo $view->render('core/PopupMenu.php', array ('items' => $items, 'uid' => $uid)); ?>
</div>