<?php
$id = md5(uniqid(time().$id,true));
$modify_item = array_shift(array_values($items));
?>
<div class="btn-group row-group">
    <div class="btn transparent-btn title" style="color: #000 !important;">
        <span class="title" onclick="<?=$modify_item['url']?>"><?=$title?></span>
    </div>
</div>
<?php
if ( count($items) > 0 ) {
    ?>
    <div class="btn-group row-group-btn more-actions">
        <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown">
            <span class="label">...</span>
        </div>
        <?php echo $view->render('core/PopupMenu.php', array('items' => $items)); ?>
    </div>
    <?php
}