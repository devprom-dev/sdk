<?php
$id = md5(uniqid(time().$id,true));
?>
<div class="btn-group alert-filter">
    <div class="btn transparent-btn title" style="color: #000 !important;">
        <span class="title"><?=$title?></span>
    </div>
</div>
<?php
if ( count($items) > 0 ) {
    ?>
    <div class="btn-group row-group-btn more-actions">
        <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" data-target="#linkmenu<?= $id ?>">
            <span class="label">...</span>
        </div>
    </div>
    <div class="btn-group dropdown-fixed" id="linkmenu<?= $id ?>">
        <?php echo $view->render('core/PopupMenu.php', array('items' => $items)); ?>
    </div>
    <?php
}