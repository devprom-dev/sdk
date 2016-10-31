<?php
$buttons = array();
foreach( $actions as $key => $action ) {
    if ( $action['view'] == 'button' ) {
        $buttons[$action['button-class']][] = $action;
        unset($actions[$key]);
    }
}
?>
<div class="btn-group">
</div>

<?php foreach( $buttons as $buttonsForClass ) { ?>
<div class="btn-group">
    <?php
    if ( count($buttonsForClass) > 5 ) {
        ?>
        <a class="btn btn-small dropdown-toggle btn-warning" href="#" data-toggle="dropdown">
            <?=translate('Состояние')?>
            <span class="caret"></span>
        </a>
        <? echo $view->render('core/PopupMenu.php', array ('items' => $buttonsForClass)); ?>
        <?php
    }
    else {
        foreach( $buttonsForClass as $button ) { ?>
            <a id="<?=$button['uid']?>" class="btn btn-small <?=$button['button-class']?>" href="<?=$button['url']?>" title="<?=$button['title']?>">
                <?php if ( $button['icon'] != '' ) { ?> <i class="icon-white <?=$button['icon']?>"></i><?php } ?>
                <?=$button['name']?>
            </a>
        <?php
        }
    }
?>
</div>
<?php } ?>

<div class="btn-group operation last">
    <a class="btn btn-small dropdown-toggle btn-inverse" href="#" data-toggle="dropdown">
        <?=translate('Действия')?>
        <span class="caret"></span>
    </a>
    <? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
</div>