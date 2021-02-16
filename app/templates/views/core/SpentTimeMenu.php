<div class="btn-group">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="" style="padding:0;">
        <span class="title"><?=$title?></span>
    </div>
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>
