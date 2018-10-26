<?php if ( count($chartSettingsItems) > 0 ) { ?>
    <div class="hidden-print filter">
        <?php
        foreach ($chartSettingsItems as $filter) {
            $title = $filter['title'];
            $selected = false;
            foreach ( $filter['items'] as $item ) {
                if ( $item['checked'] ) {
                    $title .= ': '.$item['name'];
                    $selected = true;
                    break;
                }
            }
            ?>
            <div class="btn-group pull-left">
                <a class="btn btn-sm dropdown-toggle <?= ($selected ? 'btn-info' : 'btn-light') ?>" uid="<?=$filter['name']?>" href="#" data-toggle="dropdown" data-target="#chartsettings<?=$filter['name']?>">
                    <?=$title?>
                    <span class="caret"></span>
                </a>
            </div>
            <div class="btn-group dropdown-fixed" id="chartsettings<?=$filter['name']?>">
                <? echo $view->render('core/PopupMenu.php', array('items' => $filter['items'], 'uid' => $filter['name'])); ?>
            </div>
        <?php } ?>
    </div>
    <div class="clearfix">
    </div>
    <br/>
<?php } ?>

<? if ( $demo_hint != '' ) { ?>
<div class="alert alert-hint">
    <?=$demo_hint?>
</div>
<? } ?>

<?php
$list->draw($this);
?>

<div class="clearfix"></div>
<br/>