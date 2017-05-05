<?php
$buttons = array();
foreach( $actions as $key => $action ) {
    if ( $action['view'] == 'button' ) {
        $buttons[$action['button-class']][] = $action;
        unset($actions[$key]);
    }
    if ( is_array($action['items']) ) {
        foreach( $action['items'] as $itemKey => $itemAction ) {
            if ( $itemAction['view'] == 'button' ) {
                if ( !is_array($buttons[$itemAction['button-class']]) ) {
                    $buttons = array_merge(array(
                        $itemAction['button-class'] => array()
                    ), $buttons);
                }
                $buttons[$itemAction['button-class']][] = $itemAction;
                unset($actions[$key]['items'][$itemKey]);
            }
        }
    }
}
?>
<div class="btn-group">
</div>

<?php foreach( $buttons as $buttonClass => $buttonsForClass ) { ?>
<div class="btn-group">
    <?php
    if ( count($buttonsForClass) > 5 ) {
        ?>
        <a class="btn btn-small dropdown-toggle <?=$buttonClass?>" href="#" data-toggle="dropdown">
            <?=($buttonClass == '' ? translate('Состояние') : translate('Создать'))?>
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

<?php
foreach( $sections as $section ) {
    if ( $section instanceof PageSectionComments ) {
        ?>
        <div class="btn-group">
            <a id="comment-shortcut" class="btn btn-small" href="javascript:clickAddCommentOnForm();">
                <i class="icon-comment"></i>
            </a>
        </div>
        <?php
        break;
    }
}
?>

<div class="btn-group operation last">
    <a class="btn btn-small dropdown-toggle btn-inverse" href="#" data-toggle="dropdown">
        <?=translate('Действия')?>
        <span class="caret"></span>
    </a>
    <? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
</div>