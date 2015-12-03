<?
$modify_item = array_shift(array_values($items));
?>
<div class="btn-group row-group">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="#">
        <span class="title" ondblclick="<?=$modify_item['url']?>"><?=$title?></span>
        <?php if ( count($items) > 0 ) { ?>
        <span class="caret"></span>
        <?php } ?>
    </div>

    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>