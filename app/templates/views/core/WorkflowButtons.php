<div style="padding-bottom: 6px;">
    <?=$title?>
</div>
<div class="filter-actions">

    <? if ( count($actions) > 0 ) { ?>
    <div class="btn-group pull-left">
        <a class="btn dropdown-toggle btn-sm btn-warning" href="" data-toggle="dropdown">
            <i class="icon-hand-right"></i> <?=translate("Состояние")?>
            <span class="caret"></span>
        </a>
        <? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
    </div>
    <? } ?>

    <? if ( count($relatedActions) > 0 ) { $item = array_shift($relatedActions); ?>
    <div class="btn-group pull-left">
        <a id="<?=$item['uid']?>" class="btn btn-sm btn-light" href="<?=$item['url']?>">
            <i class="icon-plus"></i> <?=$item['name']?>
        </a>
    </div>
    <? } ?>

</div>
<div class="clearfix"></div>